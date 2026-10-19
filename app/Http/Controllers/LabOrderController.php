<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabOrderRequest;
use App\Models\{Admission, DiagnosticTest, LabOrder};
use App\Services\LabService;
use App\Support\{Format, Registry};
use Illuminate\Http\Request;

class LabOrderController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('lab_orders');
        $query = LabOrder::with('patient', 'test');
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['admission_id', 'ordered_at', 'status'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('patient', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('test', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }
        $records = $query->latest('id')->paginate(25)->withQueryString();

        return view('lab_orders.index', ['module' => 'lab_orders', 'records' => $records]);
    }

    public function create()
    {
        Registry::authorize('lab_orders', true);
        $tests = DiagnosticTest::where('active', true)->orderBy('name')->get()
            ->mapWithKeys(fn($t) => [$t->id => $t->name . ' — ' . Format::money($t->price)]);
        $admissions = Admission::with('patient')->where('status', 'admitted')->latest('id')->get()
            ->mapWithKeys(fn($a) => [$a->id => 'ADM-' . $a->id . ' · ' . ($a->patient?->name ?? '')]);

        return view('lab_orders.form', ['module' => 'lab_orders', 'tests' => $tests, 'admissions' => $admissions]);
    }

    public function store(LabOrderRequest $request, LabService $service)
    {
        $order = $service->create($request->validated());

        return redirect()->route('lab_orders.show', $order)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(LabOrder $lab_order)
    {
        Registry::authorize('lab_orders');
        $lab_order->load('patient', 'test', 'results');

        return view('lab_orders.show', ['module' => 'lab_orders', 'record' => $lab_order]);
    }
}
