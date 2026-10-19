<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdmissionRequest;
use App\Models\{Admission, Bed};
use App\Services\AdmissionService;
use App\Support\{Audit, Registry};
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('admissions');
        $query = Admission::with('patient', 'doctor');
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['admitted_at', 'reason', 'status'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('patient', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('doctor', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('admissions.index', ['module' => 'admissions', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('admissions', true);
        $beds = Bed::where('state', 'available')
            ->whereDoesntHave('stays', fn($q) => $q->whereNull('ended_at'))
            ->orderBy('name')->pluck('name', 'id');

        return view('admissions.form', ['module' => 'admissions', 'record' => new Admission, 'beds' => $beds]);
    }

    public function store(AdmissionRequest $request, AdmissionService $service)
    {
        $data = $request->validated();
        $data['admitted_at'] = Carbon::parse($data['admitted_at'])->toDateTimeString();
        $admission = $service->create($data);

        return redirect()->route('admissions.show', $admission)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(Admission $admission)
    {
        Registry::authorize('admissions');
        $admission->load('patient', 'doctor', 'invoice');

        return view('admissions.show', ['module' => 'admissions', 'record' => $admission]);
    }

    public function edit(Admission $admission)
    {
        Registry::authorize('admissions', true);

        return view('admissions.form', ['module' => 'admissions', 'record' => $admission, 'beds' => collect()]);
    }

    public function update(AdmissionRequest $request, Admission $admission)
    {
        DB::transaction(function () use ($request, $admission) {
            $admission = Admission::lockForUpdate()->findOrFail($admission->id);
            if ($admission->status !== 'admitted') throw new DomainException('ভর্তি চলমান না থাকলে সম্পাদনা নয়।');
            $before = $admission->toArray();
            $admission->fill($request->validated())->save();
            Audit::record('update', $admission->getTable(), $admission->id, ['before' => $before, 'after' => $admission->toArray()]);
        });

        return redirect()->route('admissions.show', $admission)->with('success', 'তথ্য আপডেট হয়েছে।');
    }
}
