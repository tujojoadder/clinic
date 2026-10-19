<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\Visit;
use App\Services\VisitService;
use App\Support\Registry;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('visits');
        $query = Visit::with('patient', 'doctor.specialty', 'reference');

        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['appointment_at', 'serial_no', 'visit_type', 'fee', 'status', 'notes'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('patient', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('doctor', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('doctor.specialty', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('reference', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        // ড্রপডাউন ফিল্টার (সব AND হিসেবে কাজ করবে)
        if ($request->filled('specialty_id')) {
            $query->whereHas('doctor', fn($q) => $q->where('specialty_id', (int) $request->specialty_id));
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', (int) $request->doctor_id);
        }
        if (in_array($request->visit_type, ['new', 'followup'], true)) {
            $query->where('visit_type', $request->visit_type);
        }
        if ($request->filled('status')) {
            $query->where('status', (string) $request->status);
        }

        $records = $query->latest('id')->paginate(per_page())->withQueryString();

        return view('visits.index', [
            'module'      => 'visits',
            'records'     => $records,
            'specialties' => Specialty::orderBy('name')->get(['id', 'name']),
            'doctors'     => Doctor::orderBy('name')->get(['id', 'name']),
            'statuses'    => Visit::query()->distinct()->orderBy('status')->pluck('status'),
        ]);
    }

    public function create()
    {
        Registry::authorize('visits', true);

        return view('visits.form', ['module' => 'visits', 'record' => new Visit]);
    }

    public function store(VisitRequest $request, VisitService $service)
    {
        $data = $request->validated();

        // নতুন ভিজিট সবসময় waiting দিয়ে শুরু হবে
        $data['status'] = 'waiting';

        // নতুন ভিজিটে রোগীর বর্তমান রেফারেন্স বসবে
        $data['reference_id'] = Patient::find($data['patient_id'])?->reference_id;

        $visit = $service->save($data);

        return redirect()->route('visits.show', $visit)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(Visit $visit)
    {
        Registry::authorize('visits');
        $visit->load('patient', 'doctor', 'reference', 'invoice');

        return view('visits.show', ['module' => 'visits', 'record' => $visit]);
    }

    public function edit(Visit $visit)
    {
        Registry::authorize('visits', true);

        return view('visits.form', ['module' => 'visits', 'record' => $visit]);
    }

    public function update(VisitRequest $request, Visit $visit, VisitService $service)
    {
        $data = $request->validated();

        // এডিটে reference_id বদলানো হয় না, ভিজিটের আসল রেফারেন্সই থাকে
        unset($data['reference_id']);

        $service->save($data, $visit);

        return redirect()->route('visits.show', $visit)->with('success', 'তথ্য আপডেট হয়েছে।');
    }
}
