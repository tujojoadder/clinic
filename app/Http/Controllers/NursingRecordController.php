<?php

namespace App\Http\Controllers;

use App\Http\Requests\NursingRecordRequest;
use App\Models\{Admission, NursingRecord};
use App\Support\{Audit, Registry};
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NursingRecordController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('nursing_records');
        $query = NursingRecord::with('admission.patient');

        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['admission_id', 'recorded_at', 'blood_pressure', 'temperature', 'pulse', 'notes'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('admission.patient', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        $records = $query->latest('id')->paginate(per_page())->withQueryString();

        return view('nursing_records.index', ['module' => 'nursing_records', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('nursing_records', true);

        return view('nursing_records.form', [
            'module' => 'nursing_records',
            'record' => new NursingRecord,
            'admissions' => $this->admissionOptions(),
        ]);
    }

    public function store(NursingRecordRequest $request)
    {
        $record = DB::transaction(function () use ($request) {
            $data = $this->prepare($request->validated());
            $data['user_id'] = auth()->id();
            $record = NursingRecord::create($data);
            Audit::record('create', $record->getTable(), $record->id, ['before' => [], 'after' => $record->toArray()]);

            return $record;
        });

        return redirect()->route('nursing_records.show', $record)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(NursingRecord $nursing_record)
    {
        Registry::authorize('nursing_records');
        $nursing_record->load('admission.patient');

        return view('nursing_records.show', ['module' => 'nursing_records', 'record' => $nursing_record]);
    }

    public function edit(NursingRecord $nursing_record)
    {
        Registry::authorize('nursing_records', true);

        return view('nursing_records.form', [
            'module' => 'nursing_records',
            'record' => $nursing_record,
            'admissions' => $this->admissionOptions($nursing_record->admission_id),
        ]);
    }

    public function update(NursingRecordRequest $request, NursingRecord $nursing_record)
    {
        DB::transaction(function () use ($request, $nursing_record) {
            $record = NursingRecord::lockForUpdate()->findOrFail($nursing_record->id);
            $data = $this->prepare($request->validated());
            $before = $record->toArray();
            $record->fill($data)->save();
            Audit::record('update', $record->getTable(), $record->id, ['before' => $before, 'after' => $record->toArray()]);
        });

        return redirect()->route('nursing_records.show', $nursing_record)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(NursingRecord $nursing_record)
    {
        Registry::authorize('nursing_records', true);
        DB::transaction(function () use ($nursing_record) {
            $id = $nursing_record->id;
            $table = $nursing_record->getTable();
            $snapshot = $nursing_record->toArray();
            $nursing_record->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('nursing_records.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }

    private function prepare(array $data): array
    {
        if (Admission::findOrFail($data['admission_id'])->status !== 'admitted') {
            throw new DomainException('রোগী বর্তমানে ভর্তি নেই।');
        }
        $data['recorded_at'] = Carbon::parse($data['recorded_at'])->toDateTimeString();

        return $data;
    }

    private function admissionOptions(?int $current = null)
    {
        return Admission::with('patient')
            ->where(fn($q) => $q->where('status', 'admitted')->when($current, fn($q) => $q->orWhere('id', $current)))
            ->latest('id')->get()
            ->mapWithKeys(fn($a) => [$a->id => 'ADM-' . $a->id . ' · ' . ($a->patient?->name ?? '')]);
    }
}
