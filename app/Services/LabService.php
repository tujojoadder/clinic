<?php

namespace App\Services;

use App\Models\{Admission, DiagnosticTest, Invoice, LabOrder, LabResult};
use App\Support\Audit;
use DomainException;
use Illuminate\Support\Facades\DB;

class LabService
{
    public function create(array $data): LabOrder
    {
        return DB::transaction(function () use ($data) {
            $test = DiagnosticTest::findOrFail($data['test_id']);
            if (!$test->active) throw new DomainException('টেস্ট নিষ্ক্রিয়।');
            if (!empty($data['admission_id'])) {
                $admission = Admission::lockForUpdate()->findOrFail($data['admission_id']);
                if ($admission->status !== 'admitted' || $admission->patient_id != $data['patient_id']) throw new DomainException('রোগী ও ভর্তি মিলছে না।');
                $invoice = $admission->invoice()->firstOrFail();
            } else {
                $invoice = Invoice::create(['patient_id' => $data['patient_id']]);
            }
            $order = LabOrder::create($data + ['invoice_id' => $invoice->id]);
            app(BillingService::class)->addItem($invoice, ['description' => $test->name, 'quantity' => 1, 'unit_price' => $test->price, 'kind' => 'test', 'lab_order_id' => $order->id]);
            Audit::record('create', 'lab_orders', $order->id);
            return $order;
        });
    }

    public function advance(LabOrder $order, string $status): void
    {
        DB::transaction(function () use ($order, $status) {
            $order = LabOrder::lockForUpdate()->findOrFail($order->id);
            $next = ['ordered' => 'collected', 'collected' => 'processing', 'processing' => 'review'];
            if (($next[$order->status] ?? '') !== $status) throw new DomainException('সঠিক পরবর্তী ধাপ নির্বাচন করুন।');
            $data = ['status' => $status];
            if ($status === 'collected') $data['collected_at'] = now();
            $order->update($data);
            Audit::record($status, 'lab_orders', $order->id);
        });
    }

    public function results(LabOrder $order, array $values, ?string $notes): void
    {
        DB::transaction(function () use ($order, $values, $notes) {
            $order = LabOrder::lockForUpdate()->findOrFail($order->id);
            if (in_array($order->status, ['approved', 'cancelled'])) throw new DomainException('অনুমোদিত বা বাতিল রিপোর্ট পরিবর্তন নয়।');
            $parameters = $order->test->parameters()->orderBy('sort_order')->get();
            if ($parameters->isEmpty()) throw new DomainException('টেস্টের রিপোর্ট টেমপ্লেট তৈরি করুন।');
            foreach ($parameters as $parameter) {
                $value = trim((string) ($values[$parameter->id] ?? ''));
                if ($value === '') continue;
                $order->results()->updateOrCreate(['parameter_name' => $parameter->name], [
                    'result_value' => $value, 'unit' => $parameter->unit, 'reference_range' => $parameter->reference_range,
                ]);
            }
            $order->update(['report_notes' => $notes]);
            Audit::record('results', 'lab_orders', $order->id);
        });
    }

    public function approve(LabOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $order = LabOrder::lockForUpdate()->findOrFail($order->id);
            if ($order->status !== 'review') throw new DomainException('রিপোর্ট রিভিউ ধাপে নেই।');
            $parameters = $order->test->parameters;
            if ($parameters->isEmpty()) throw new DomainException('রিপোর্ট টেমপ্লেট নেই।');
            foreach ($parameters as $parameter) {
                if (!$order->results()->where('parameter_name', $parameter->name)->where('result_value', '!=', '')->exists()) throw new DomainException('সব প্যারামিটারের ফলাফল পূরণ করুন।');
            }
            $order->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => auth()->id()]);
            Audit::record('approve', 'lab_orders', $order->id);
        });
    }

    public function reopen(LabOrder $order, string $reason): void
    {
        DB::transaction(function () use ($order, $reason) {
            $order = LabOrder::lockForUpdate()->findOrFail($order->id);
            if ($order->status !== 'approved') throw new DomainException('রিপোর্ট অনুমোদিত নয়।');
            Audit::record('revision', 'lab_orders', $order->id, ['reason' => $reason, 'old_results' => $order->results()->get()->toArray()]);
            $order->update(['status' => 'processing', 'approved_at' => null, 'approved_by' => null]);
        });
    }

    public function cancel(LabOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $order = LabOrder::lockForUpdate()->findOrFail($order->id);
            if (in_array($order->status, ['approved', 'cancelled'])) throw new DomainException('এই অর্ডার বাতিল নয়।');
            $invoice = app(BillingService::class)->lock($order->invoice_id);
            if ($invoice->status !== 'open') throw new DomainException('বিল চূড়ান্ত হয়েছে।');
            $invoice->items()->where('lab_order_id', $order->id)->delete();
            $totals = app(BillingService::class)->totals($invoice);
            if ($totals['discount'] > $totals['subtotal'] || (!$invoice->admission_id && $totals['paid'] > $totals['total'])) throw new DomainException('আগে ছাড়/অতিরিক্ত আদায় সমন্বয় করুন।');
            $order->update(['status' => 'cancelled']);
            Audit::record('cancel', 'lab_orders', $order->id);
        });
    }
}
