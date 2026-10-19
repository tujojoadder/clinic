<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use App\Support\Audit;
use App\Support\Registry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('patients');

        $query = Patient::query();

        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);

            $query->where(function ($query) use ($search) {
                foreach (
                    [
                        'name',
                        'birth_date',
                        'age_years',
                        'gender',
                        'phone',
                        'address',
                        'emergency_contact',
                    ] as $column
                ) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        $records = $query
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('patients.index', [
            'module'  => 'patients',
            'records' => $records,
        ]);
    }

    public function create()
    {
        Registry::authorize('patients', true);

        return view('patients.form', [
            'module' => 'patients',
            'record' => new Patient(),
        ]);
    }

    public function store(PatientRequest $request)
    {
        $patient = $this->createPatient($request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    /**
     * Create a patient using AJAX from a modal.
     */
    public function quickStore(PatientRequest $request)
    {
        Registry::authorize('patients', true);

        $patient = $this->createPatient($request->validated());

        return response()->json([
            'id'    => $patient->id,
            'label' => $patient->name,
        ], 201);
    }

    /**
     * Select2 AJAX patient search.
     */
    public function search(Request $request)
    {
        Registry::authorize('patients');

        $search = mb_substr((string) $request->query('q', ''), 0, 150);

        $page = Patient::query()
            ->when(
                $search !== '',
                fn($query) => $query->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
            )
            ->latest('id')
            ->paginate(10);

        return response()->json([
            'results' => $page->getCollection()
                ->map(fn($patient) => [
                    'id'   => $patient->id,
                    'text' => $patient->name,
                ])
                ->values(),

            'pagination' => [
                'more' => $page->hasMorePages(),
            ],
        ]);
    }

    /**
     * Create a patient and record the audit log.
     */
    private function createPatient(array $data): Patient
    {
        return DB::transaction(function () use ($data) {
            $patient = Patient::create($data);

            Audit::record(
                'create',
                $patient->getTable(),
                $patient->id,
                [
                    'before' => [],
                    'after'  => $patient->toArray(),
                ]
            );

            return $patient;
        });
    }

    public function show(Patient $patient)
    {
        Registry::authorize('patients');

        return view('patients.show', [
            'module' => 'patients',
            'record' => $patient,
        ]);
    }

    public function edit(Patient $patient)
    {
        Registry::authorize('patients', true);

        return view('patients.form', [
            'module' => 'patients',
            'record' => $patient,
        ]);
    }

    public function update(
        PatientRequest $request,
        Patient $patient
    ) {
        DB::transaction(function () use ($request, $patient) {
            $patient = Patient::query()
                ->lockForUpdate()
                ->findOrFail($patient->id);

            $before = $patient->toArray();

            $patient->fill($request->validated());
            $patient->save();

            Audit::record(
                'update',
                $patient->getTable(),
                $patient->id,
                [
                    'before' => $before,
                    'after'  => $patient->toArray(),
                ]
            );
        });

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Patient $patient)
    {
        Registry::authorize('patients', true);

        DB::transaction(function () use ($patient) {
            $id = $patient->id;
            $table = $patient->getTable();
            $snapshot = $patient->toArray();

            $patient->delete();

            Audit::record(
                'delete',
                $table,
                $id,
                $snapshot
            );
        });

        return redirect()
            ->route('patients.index')
            ->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }
}
