<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialtyRequest;
use App\Models\Specialty;
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecialtyController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('specialties');
        $query = Specialty::query()->withCount('doctors');
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . mb_substr((string) $request->q, 0, 150) . '%');
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('specialties.index', ['module' => 'specialties', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('specialties', true);

        return view('specialties.form', ['module' => 'specialties', 'record' => new Specialty]);
    }

    public function store(SpecialtyRequest $request)
    {
        $specialty = $this->createSpecialty($request->validated());

        if ($request->expectsJson()) {
            return response()->json($this->payload($specialty), 201);
        }

        return redirect()->route('specialties.index')->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    // Ajax (modal) থেকে তৈরি
    public function quickStore(SpecialtyRequest $request)
    {
        $specialty = $this->createSpecialty($request->validated());

        return response()->json(['id' => $specialty->id, 'label' => $specialty->name], 201);
    }

    // Select2 ajax search
    public function search(Request $request)
    {
        Registry::authorize('specialties');
        $search = mb_substr((string) $request->q, 0, 150);

        $page = Specialty::query()
            ->when($search !== '', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->orderBy('name')->paginate(10);

        return response()->json([
            'results'    => $page->getCollection()->map(fn($r) => ['id' => $r->id, 'text' => $r->name])->values(),
            'pagination' => ['more' => $page->hasMorePages()],
        ]);
    }

    private function createSpecialty(array $data): Specialty
    {
        return DB::transaction(function () use ($data) {
            $specialty = Specialty::create($data);
            Audit::record('create', $specialty->getTable(), $specialty->id, ['before' => [], 'after' => $specialty->toArray()]);

            return $specialty;
        });
    }

    public function show(Specialty $specialty)
    {
        Registry::authorize('specialties');
        $specialty->loadCount('doctors');

        return view('specialties.show', ['module' => 'specialties', 'record' => $specialty]);
    }

    public function edit(Specialty $specialty)
    {
        Registry::authorize('specialties', true);

        return view('specialties.form', ['module' => 'specialties', 'record' => $specialty]);
    }

    public function update(SpecialtyRequest $request, Specialty $specialty)
    {
        DB::transaction(function () use ($request, $specialty) {
            $specialty = Specialty::lockForUpdate()->findOrFail($specialty->id);
            $before = $specialty->toArray();
            $specialty->fill($request->validated())->save();
            Audit::record('update', $specialty->getTable(), $specialty->id, ['before' => $before, 'after' => $specialty->toArray()]);
        });

        if ($request->expectsJson()) {
            return response()->json($this->payload($specialty->refresh()));
        }

        return redirect()->route('specialties.index')->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    private function payload(Specialty $specialty): array
    {
        return [
            'id'            => $specialty->id,
            'name'          => $specialty->name,
            'doctors_count' => $specialty->doctors()->count(),
        ];
    }

    public function destroy(Request $request, Specialty $specialty)
    {
        Registry::authorize('specialties', true);
        if ($specialty->doctors()->exists()) {
            $msg = 'এই বিভাগে ডাক্তার আছে; আগে ডাক্তারদের বিভাগ বদলান।';

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 422)
                : back()->withErrors(['name' => $msg]);
        }
        DB::transaction(function () use ($specialty) {
            $id = $specialty->id;
            $table = $specialty->getTable();
            $snapshot = $specialty->toArray();
            $specialty->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        if ($request->expectsJson()) {
            return response()->json(['deleted' => true]);
        }

        return redirect()->route('specialties.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
