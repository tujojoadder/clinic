<?php

namespace App\Services;

use App\Models\{Invoice, InvoiceItem, Payment};
use App\Support\Audit;
use DomainException;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function totals(Invoice $invoice): array
    {
        $subtotal = (float) $invoice->items()->selectRaw('COALESCE(SUM(quantity * unit_price), 0) AS total')->value('total');
        $paid = (float) $invoice->payments()->whereNull('voided_at')->selectRaw("COALESCE(SUM(CASE WHEN type='receipt' THEN amount ELSE -amount END), 0) AS total")->value('total');
        $total = round($subtotal - (float) $invoice->discount, 2);
        return ['subtotal' => $subtotal, 'discount' => (float) $invoice->discount, 'total' => $total, 'paid' => $paid, 'due' => round($total - $paid, 2)];
    }

    public function lock(int $id): Invoice
    {
        return Invoice::lockForUpdate()->findOrFail($id);
    }

    public function addItem(Invoice $invoice, array $data): InvoiceItem
    {
        $invoice = $this->lock($invoice->id);
        if ($invoice->status !== 'open') throw new DomainException('বিল চলমান নেই।');
        return $invoice->items()->create($data);
    }

    public function payment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice = $this->lock($invoice->id);
            if ($invoice->status === 'void') throw new DomainException('বাতিল বিলে পেমেন্ট নয়।');
            if ($invoice->admission_id && $invoice->status === 'open') app(AdmissionService::class)->syncRent($invoice->admission);
            $totals = $this->totals($invoice);
            $amount = round((float) $data['amount'], 2);
            if ($data['type'] === 'refund') {
                abort_unless(auth()->user()->role === 'admin', 403);
                if ($amount > $totals['paid']) throw new DomainException('আদায়ের বেশি রিফান্ড নয়।');
                if (empty($data['reason'])) throw new DomainException('রিফান্ডের কারণ লিখুন।');
            } elseif ($amount > $totals['due'] && (!$invoice->admission_id || $invoice->status === 'finalized')) {
                throw new DomainException('বকেয়ার বেশি টাকা নয়।');
            }
            $payment = $invoice->payments()->create($data + ['paid_at' => now(), 'user_id' => auth()->id()]);
            Audit::record($data['type'], 'payments', $payment->id, ['invoice_id' => $invoice->id, 'amount' => $amount]);
            return $payment;
        });
    }

    public function saveService(Invoice $invoice, array $data, ?InvoiceItem $item = null): void
    {
        DB::transaction(function () use ($invoice, $data, $item) {
            $invoice = $this->lock($invoice->id);
            if ($invoice->status !== 'open') throw new DomainException('চূড়ান্ত বিল পরিবর্তন নয়।');
            if ($item) {
                if ($item->invoice_id !== $invoice->id || $item->kind !== 'service') throw new DomainException('স্বয়ংক্রিয় চার্জ সম্পাদনা নয়।');
                $item->update($data);
            } else {
                $this->addItem($invoice, $data + ['kind' => 'service']);
            }
            Audit::record('service_charge', 'invoices', $invoice->id, $data);
        });
    }

    public function deleteService(Invoice $invoice, InvoiceItem $item): void
    {
        DB::transaction(function () use ($invoice, $item) {
            $invoice = $this->lock($invoice->id);
            if ($invoice->status !== 'open' || $item->invoice_id !== $invoice->id || $item->kind !== 'service') throw new DomainException('এই চার্জ মোছা যাবে না।');
            $item->delete();
            if ($this->totals($invoice)['subtotal'] < $invoice->discount) throw new DomainException('আগে ছাড় কমান।');
            Audit::record('delete_service', 'invoices', $invoice->id);
        });
    }

    public function voidPayment(Invoice $invoice, Payment $payment, string $reason): void
    {
        DB::transaction(function () use ($invoice, $payment, $reason) {
            $invoice = $this->lock($invoice->id);
            if ($payment->invoice_id !== $invoice->id || $payment->voided_at) throw new DomainException('পেমেন্ট মিলছে না বা বাতিল।');
            if ($payment->type === 'receipt' && $payment->amount > $this->totals($invoice)['paid']) throw new DomainException('আগে রিফান্ড সমন্বয় করুন।');
            $payment->update(['voided_at' => now(), 'void_reason' => $reason]);
            Audit::record('void', 'payments', $payment->id, ['reason' => $reason]);
        });
    }

    public function finalize(Invoice $invoice, string $state): void
    {
        DB::transaction(function () use ($invoice, $state) {
            $invoice = $this->lock($invoice->id);
            if ($invoice->status !== 'open' || $invoice->admission_id) throw new DomainException('ভর্তি বিল ডিসচার্জ থেকে চূড়ান্ত হবে।');
            if ($state === 'void') {
                abort_unless(auth()->user()->role === 'admin', 403);
                if ($this->totals($invoice)['paid'] != 0 || \App\Models\LabOrder::where('invoice_id', $invoice->id)->where('status', '!=', 'cancelled')->exists()) throw new DomainException('আদায় ও সক্রিয় টেস্ট আগে নিষ্পত্তি করুন।');
            }
            $invoice->update(['status' => $state]);
            Audit::record($state, 'invoices', $invoice->id);
        });
    }
}
