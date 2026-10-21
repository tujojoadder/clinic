<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\{Invoice, Patient};
use App\Services\BillingService;
use App\Support\{Audit, Registry};
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        Registry::authorize('invoices');
        $query = Invoice::with('patient');
        if ($request->filled('q')) {
            $search = mb_substr((string) $request->q, 0, 150);
            $query->where(function ($query) use ($search) {
                foreach (['discount', 'notes', 'status'] as $column) {
                    $query->orWhere($column, 'like', '%' . $search . '%');
                }
                $query->orWhereHas('patient', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        // ফিল্টার (সব AND হিসেবে কাজ করবে)
        if ($request->filled('patient_id')) {
            $query->where('patient_id', (int) $request->patient_id);
        }
        if ($request->filled('status')) {
            $query->where('status', (string) $request->status);
        }

        $records = $query->latest('id')->paginate(per_page())->withQueryString();

        return view('invoices.index', [
            'module'        => 'invoices',
            'records'       => $records,
            // রোগী ফিল্টার ajax দিয়ে আসে, তাই শুধু নির্বাচিত রোগীটাই লোড হয়
            'filterPatient' => $request->filled('patient_id') ? Patient::find((int) $request->patient_id) : null,
            'statuses'      => Invoice::query()->distinct()->orderBy('status')->pluck('status'),
        ]);
    }

    public function create()
    {
        Registry::authorize('invoices', true);

        return view('invoices.form', ['module' => 'invoices', 'record' => new Invoice]);
    }

    public function store(InvoiceRequest $request)
    {
        $data = $request->validated();
        if ($data['discount'] > 0) throw new DomainException('চার্জ যোগ করার পরে ছাড় দিন।');

        $invoice = DB::transaction(function () use ($data) {
            $invoice = Invoice::create($data);
            Audit::record('create', $invoice->getTable(), $invoice->id, ['before' => [], 'after' => $invoice->toArray()]);

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'তথ্য সংরক্ষণ হয়েছে।');
    }

    public function show(Invoice $invoice)
    {
        Registry::authorize('invoices');
        $invoice->load('patient', 'items', 'payments.account');

        return view('invoices.show', ['module' => 'invoices', 'record' => $invoice]);
    }

    public function edit(Invoice $invoice)
    {
        Registry::authorize('invoices', true);

        return view('invoices.form', ['module' => 'invoices', 'record' => $invoice]);
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        DB::transaction(function () use ($request, $invoice) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoice->id);
            $data = $request->validated();
            if ($invoice->status !== 'open') throw new DomainException('চূড়ান্ত বিল সম্পাদনা নয়।');
            $totals = app(BillingService::class)->totals($invoice);
            if ($invoice->visit_id || $invoice->admission_id || $totals['paid'] > 0) $data['patient_id'] = $invoice->patient_id;
            if ($data['discount'] > $totals['subtotal']) throw new DomainException('ছাড় চার্জের বেশি নয়।');
            if ($data['discount'] != $invoice->discount && auth()->user()->role !== 'admin') throw new DomainException('ছাড় পরিবর্তনে অ্যাডমিন প্রয়োজন।');
            $before = $invoice->toArray();
            $invoice->fill($data)->save();
            Audit::record('update', $invoice->getTable(), $invoice->id, ['before' => $before, 'after' => $invoice->toArray()]);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'তথ্য আপডেট হয়েছে।');
    }
}
