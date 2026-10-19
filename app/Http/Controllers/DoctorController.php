<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Models\{Day, Doctor, Specialty};
use App\Support\{Audit, Registry};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('doctors');
        $query = Doctor::query()->with(['days', 'specialty']);
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['name', 'phone', 'fee', 'followup_fee', 'start_time', 'end_time', 'active'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('specialty', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
                $query->orWhereHas('days', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('doctors.index', ['module' => 'doctors', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('doctors', true);

        return view('doctors.form', [
            'module' => 'doctors',
            'record' => new Doctor,
            'days'   => Day::orderBy('id')->get(),
            'specialties' => Specialty::orderBy('name')->get(),
        ]);
    }

    public function store(DoctorRequest $request)
    {
        $doctor = $this->createDoctor($request->validated());

        return redirect()->route('doctors.show', $doctor)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    // Ajax (modal) থেকে তৈরি
    public function quickStore(DoctorRequest $request)
    {
        $doctor = $this->createDoctor($request->validated());
        $doctor->load('specialty');

        return response()->json(['id' => $doctor->id, 'label' => doctorLabel($doctor)], 201);
    }

    // Select2 ajax search
    // Select2 ajax search
    public function search(Request $request)
    {
        Registry::authorize('doctors');
        $search = mb_substr((string) $request->q, 0, 150);

        // ভিজিট ফর্ম থেকে তারিখ এলে ওই দিনের (শনি=0 ... শুক্র=6) ডাক্তার
        $weekday = null;
        if ($request->filled('date')) {
            try {
                $weekday = (Carbon::parse((string) $request->query('date'))->dayOfWeek + 1) % 7;
            } catch (\Throwable $e) {
                $weekday = null;
            }
        }

        $page = Doctor::query()
            ->with('specialty:id,name')
            ->when($search !== '', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->when($weekday !== null, function ($q) use ($weekday) {
                $q->where('active', 1)->where(function ($w) use ($weekday) {
                    $w->whereHas('days', fn($d) => $d->where('days.weekday', $weekday))
                        ->orDoesntHave('days'); // যার দিন ঠিক করা নেই সে সব দিন দেখাবে
                });
            })
            ->latest('id')->paginate(10);

        return response()->json([
            'results'    => $page->getCollection()->map(fn($r) => ['id' => $r->id, 'text' => doctorLabel($r)])->values(),
            'pagination' => ['more' => $page->hasMorePages()],
        ]);
    }

    private function createDoctor(array $data): Doctor
    {
        $days = $data['days'] ?? [];
        unset($data['days']);

        return DB::transaction(function () use ($data, $days) {
            $doctor = Doctor::create($data);
            $doctor->days()->sync($days);
            Audit::record('create', $doctor->getTable(), $doctor->id, [
                'before' => [],
                'after'  => $doctor->toArray() + ['days' => array_map('intval', $days)],
            ]);

            return $doctor;
        });
    }

    public function show(Doctor $doctor)
    {
        Registry::authorize('doctors');
        $doctor->load(['days', 'specialty']);

        return view('doctors.show', ['module' => 'doctors', 'record' => $doctor]);
    }

    public function edit(Doctor $doctor)
    {
        Registry::authorize('doctors', true);
        $doctor->load('days');

        return view('doctors.form', [
            'module' => 'doctors',
            'record' => $doctor,
            'days'   => Day::orderBy('id')->get(),
            'specialties' => Specialty::orderBy('name')->get(),
        ]);
    }

    public function update(DoctorRequest $request, Doctor $doctor)
    {
        DB::transaction(function () use ($request, $doctor) {
            $data = $request->validated();
            $days = $data['days'] ?? [];
            unset($data['days']);

            $doctor = Doctor::lockForUpdate()->findOrFail($doctor->id);
            $before = $doctor->toArray() + ['days' => $doctor->days()->pluck('days.id')->all()];
            $doctor->fill($data)->save();
            $doctor->days()->sync($days);
            Audit::record('update', $doctor->getTable(), $doctor->id, [
                'before' => $before,
                'after'  => $doctor->toArray() + ['days' => array_map('intval', $days)],
            ]);
        });

        return redirect()->route('doctors.show', $doctor)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Doctor $doctor)
    {
        Registry::authorize('doctors', true);
        DB::transaction(function () use ($doctor) {
            $id = $doctor->id;
            $table = $doctor->getTable();
            $snapshot = $doctor->toArray();
            $doctor->days()->detach();
            $doctor->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('doctors.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }

    public function fees(Doctor $doctor)
    {
        Registry::authorize('visits', true);

        return response()->json([
            'fee'          => (float) $doctor->fee,
            'followup_fee' => (float) ($doctor->followup_fee ?: $doctor->fee),
        ]);
    }
}
