<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorLedgerRequest;
use App\Models\{Doctor, DoctorLedger};
use App\Support\{Audit, Registry};
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorLedgerController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('doctor_ledgers');
        $query = DoctorLedger::with('doctor');
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['type', 'amount', 'entry_date', 'notes'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('doctor', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('doctor_ledgers.index', ['module' => 'doctor_ledgers', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('doctor_ledgers', true);

        return view('doctor_ledgers.form', ['module' => 'doctor_ledgers', 'record' => new DoctorLedger]);
    }

    public function store(DoctorLedgerRequest $request)
    {
        $ledger = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $this->checkSettlement($data);
            $ledger = DoctorLedger::create($data);
            Audit::record('create', $ledger->getTable(), $ledger->id, ['before' => [], 'after' => $ledger->toArray()]);

            return $ledger;
        });

        return redirect()->route('doctor_ledgers.show', $ledger)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(DoctorLedger $doctor_ledger)
    {
        Registry::authorize('doctor_ledgers');
        $doctor_ledger->load('doctor');

        return view('doctor_ledgers.show', ['module' => 'doctor_ledgers', 'record' => $doctor_ledger]);
    }

    public function edit(DoctorLedger $doctor_ledger)
    {
        Registry::authorize('doctor_ledgers', true);

        return view('doctor_ledgers.form', ['module' => 'doctor_ledgers', 'record' => $doctor_ledger]);
    }

    public function update(DoctorLedgerRequest $request, DoctorLedger $doctor_ledger)
    {
        DB::transaction(function () use ($request, $doctor_ledger) {
            $ledger = DoctorLedger::lockForUpdate()->findOrFail($doctor_ledger->id);
            $data = $request->validated();
            $this->checkSettlement($data, $ledger->id);
            $before = $ledger->toArray();
            $ledger->fill($data)->save();
            Audit::record('update', $ledger->getTable(), $ledger->id, ['before' => $before, 'after' => $ledger->toArray()]);
        });

        return redirect()->route('doctor_ledgers.show', $doctor_ledger)->with('success', 'তথ্য আপডেট হয়েছে।');
    }

    public function destroy(DoctorLedger $doctor_ledger)
    {
        Registry::authorize('doctor_ledgers', true);
        DB::transaction(function () use ($doctor_ledger) {
            $id = $doctor_ledger->id;
            $table = $doctor_ledger->getTable();
            $snapshot = $doctor_ledger->toArray();
            $doctor_ledger->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });

        return redirect()->route('doctor_ledgers.index')->with('success', 'রেকর্ড মুছে ফেলা হয়েছে।');
    }

    private function checkSettlement(array $data, int $ignoreId = 0): void
    {
        if ($data['type'] !== 'settlement') return;
        Doctor::lockForUpdate()->findOrFail($data['doctor_id']);
        $balance = (float) DoctorLedger::where('doctor_id', $data['doctor_id'])->where('id', '!=', $ignoreId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type='payable' THEN amount ELSE -amount END), 0) AS balance")->value('balance');
        if ($data['amount'] > $balance) throw new DomainException('প্রাপ্যের বেশি নিষ্পত্তি নয়।');
    }
}
