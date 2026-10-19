<?php

namespace App\Http\Controllers;

use App\Models\{Admission, Invoice, InvoiceItem, LabOrder, Payment, Visit};
use App\Services\{AdmissionService, BillingService, LabService};
use App\Support\{Audit, Registry};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    public function action(Request $request, string $module, int $record, string $action)
    {
        $billing = app(BillingService::class);
        $admissions = app(AdmissionService::class);
        $lab = app(LabService::class);
        $expected = [
            'visit_complete' => 'visits', 'transfer' => 'admissions', 'cancel_admission' => 'admissions',
            'discharge' => 'admissions', 'sync_rent' => 'invoices', 'payment' => 'invoices',
            'void_payment' => 'invoices', 'service_save' => 'invoices', 'service_delete' => 'invoices',
            'invoice_state' => 'invoices', 'lab_status' => 'lab_orders', 'lab_results' => 'lab_orders',
            'approve_lab' => 'lab_orders', 'reopen_lab' => 'lab_orders', 'cancel_lab' => 'lab_orders',
        ];
        abort_unless(($expected[$action] ?? null) === $module, 404);
        Registry::authorize($action === 'discharge' ? 'discharge' : $module, true);
        switch ($action) {
            case 'visit_complete':
                DB::transaction(function () use ($record) {
                    $visit = Visit::lockForUpdate()->findOrFail($record);
                    if ($visit->status === 'cancelled') throw new \DomainException('ভিজিট বাতিল।');
                    $visit->update(['status' => 'completed']);
                    Audit::record('complete', 'visits', $record);
                });
                break;
            case 'transfer':
                $data = $request->validate(['bed_id' => 'required|integer|exists:beds,id', 'at' => 'required|date|before_or_equal:now']);
                $admissions->transfer(Admission::findOrFail($record), (int) $data['bed_id'], $data['at']);
                break;
            case 'cancel_admission': $admissions->cancel(Admission::findOrFail($record)); break;
            case 'discharge':
                $data = $request->validate([
                    'discharged_at' => 'required|date|before_or_equal:now', 'treatment_summary' => 'required|string|max:10000',
                    'followup_notes' => 'nullable|string|max:10000', 'discharge_type' => ['required', Rule::in(['regular', 'transfer', 'own_risk'])],
                    'allow_due' => 'nullable|boolean',
                ]);
                $admissions->discharge(Admission::findOrFail($record), $data);
                break;
            case 'sync_rent':
                DB::transaction(function () use ($record, $billing, $admissions) {
                    $invoice = $billing->lock($record);
                    if ($invoice->admission_id) $admissions->syncRent($invoice->admission);
                });
                break;
            case 'payment':
                $data = $request->validate([
                    'amount' => 'required|numeric|gt:0|max:999999999', 'type' => ['required', Rule::in(['receipt', 'refund'])],
                    'method' => ['required', Rule::in(['cash', 'bkash', 'nagad', 'bank'])],
                    'transaction_ref' => 'nullable|string|max:150', 'reason' => 'nullable|string|max:255',
                ]);
                $billing->payment(Invoice::findOrFail($record), $data);
                break;
            case 'void_payment':
                abort_unless(auth()->user()->role === 'admin', 403);
                $data = $request->validate(['payment_id' => 'required|integer|exists:payments,id', 'reason' => 'required|string|max:255']);
                $billing->voidPayment(Invoice::findOrFail($record), Payment::findOrFail($data['payment_id']), $data['reason']);
                break;
            case 'service_save':
                $data = $request->validate(['description' => 'required|string|max:255', 'quantity' => 'required|numeric|gt:0|max:999999', 'unit_price' => 'required|numeric|min:0|max:999999999', 'item_id' => 'nullable|integer|exists:invoice_items,id']);
                $item = !empty($data['item_id']) ? InvoiceItem::findOrFail($data['item_id']) : null;
                unset($data['item_id']);
                $billing->saveService(Invoice::findOrFail($record), $data, $item);
                break;
            case 'service_delete':
                $data = $request->validate(['item_id' => 'required|integer|exists:invoice_items,id']);
                $billing->deleteService(Invoice::findOrFail($record), InvoiceItem::findOrFail($data['item_id']));
                break;
            case 'invoice_state':
                $data = $request->validate(['state' => ['required', Rule::in(['void', 'finalized'])]]);
                $billing->finalize(Invoice::findOrFail($record), $data['state']);
                break;
            case 'lab_status':
                abort_unless(in_array(auth()->user()->role, ['admin', 'lab']), 403);
                $data = $request->validate(['status' => ['required', Rule::in(['collected', 'processing', 'review'])]]);
                $lab->advance(LabOrder::findOrFail($record), $data['status']);
                break;
            case 'lab_results':
                abort_unless(in_array(auth()->user()->role, ['admin', 'lab']), 403);
                $data = $request->validate(['results' => 'required|array|max:100', 'results.*' => 'nullable|string|max:150', 'report_notes' => 'nullable|string|max:10000']);
                $lab->results(LabOrder::findOrFail($record), $data['results'], $data['report_notes'] ?? null);
                break;
            case 'approve_lab':
                abort_unless(auth()->user()->role === 'admin', 403);
                $lab->approve(LabOrder::findOrFail($record));
                break;
            case 'reopen_lab':
                abort_unless(auth()->user()->role === 'admin', 403);
                $data = $request->validate(['reason' => 'required|string|max:255']);
                $lab->reopen(LabOrder::findOrFail($record), $data['reason']);
                break;
            case 'cancel_lab': $lab->cancel(LabOrder::findOrFail($record)); break;
        }
        return redirect()->route('entities.show', [$module, $record])->with('success', 'কাজটি সম্পন্ন হয়েছে।');
    }
}
