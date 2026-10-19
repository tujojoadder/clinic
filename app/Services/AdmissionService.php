<?php

namespace App\Services;

use App\Models\{Admission, Bed, BedStay, Invoice, InvoiceItem, Patient, NursingRecord, LabOrder};
use App\Support\Audit;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class AdmissionService
{
    public static function days(string $start, string $end): int
    {
        $seconds = Carbon::parse($end)->getTimestamp() - Carbon::parse($start)->getTimestamp();
        if ($seconds < 0) throw new DomainException('শেষ সময় শুরুর সময়ের আগে নয়।');
        return max(1, (int) ceil($seconds / 86400));
    }

    public function create(array $data): Admission
    {
        return DB::transaction(function () use ($data) {
            Patient::lockForUpdate()->findOrFail($data['patient_id']);
            if (Admission::where('patient_id', $data['patient_id'])->where('status', 'admitted')->exists()) throw new DomainException('রোগী ইতিমধ্যে ভর্তি।');
            $admission = Admission::create(collect($data)->only(['patient_id', 'doctor_id', 'admitted_at', 'reason'])->all());
            $invoice = Invoice::create(['patient_id' => $admission->patient_id, 'admission_id' => $admission->id]);
            $this->assign($admission, (int) $data['bed_id'], $admission->admitted_at);
            $this->syncRent($admission);
            if (!empty($data['advance'])) app(BillingService::class)->payment($invoice, ['amount' => $data['advance'], 'method' => $data['method'], 'type' => 'receipt', 'reason' => 'Admission advance']);
            Audit::record('create', 'admissions', $admission->id);
            return $admission;
        });
    }

    private function assign(Admission $admission, int $bedId, string $at): void
    {
        $bed = Bed::lockForUpdate()->findOrFail($bedId);
        if ($bed->state !== 'available' || $bed->stays()->whereNull('ended_at')->exists()) throw new DomainException('বেড খালি নেই।');
        $admission->stays()->create(['bed_id' => $bedId, 'started_at' => $at, 'daily_rate' => $bed->daily_rate]);
    }

    public function syncRent(Admission $admission, ?string $at = null): void
    {
        $invoice = Invoice::where('admission_id', $admission->id)->lockForUpdate()->firstOrFail();
        if ($invoice->status !== 'open') return;
        foreach ($admission->stays()->with('bed')->get() as $stay) {
            $days = self::days($stay->started_at, $stay->ended_at ?? $at ?? now()->toDateTimeString());
            InvoiceItem::updateOrCreate(['bed_stay_id' => $stay->id], [
                'invoice_id' => $invoice->id, 'description' => $stay->bed->name.' · প্রতি ২৪ ঘণ্টা',
                'quantity' => $days, 'unit_price' => $stay->daily_rate, 'kind' => 'bed',
            ]);
        }
    }

    public function transfer(Admission $admission, int $bedId, string $at): void
    {
        DB::transaction(function () use ($admission, $bedId, $at) {
            $admission = Admission::lockForUpdate()->findOrFail($admission->id);
            if ($admission->status !== 'admitted') throw new DomainException('রোগী বর্তমানে ভর্তি নেই।');
            $old = $admission->stays()->whereNull('ended_at')->lockForUpdate()->firstOrFail();
            if ($old->bed_id === $bedId) throw new DomainException('আলাদা বেড নির্বাচন করুন।');
            self::days($old->started_at, $at);
            $old->update(['ended_at' => $at]);
            $old->bed->update(['state' => 'cleaning']);
            $this->assign($admission, $bedId, $at);
            $this->syncRent($admission, $at);
            Audit::record('transfer', 'admissions', $admission->id, ['old_bed' => $old->bed_id, 'new_bed' => $bedId]);
        });
    }

    public function discharge(Admission $admission, array $data): void
    {
        DB::transaction(function () use ($admission, $data) {
            $admission = Admission::lockForUpdate()->findOrFail($admission->id);
            if ($admission->status !== 'admitted' || Carbon::parse($data['discharged_at'])->lt(Carbon::parse($admission->admitted_at))) throw new DomainException('ভর্তি বা ছাড়পত্রের সময় সঠিক নয়।');
            $stay = $admission->stays()->whereNull('ended_at')->lockForUpdate()->first();
            if ($stay) {
                self::days($stay->started_at, $data['discharged_at']);
                $stay->update(['ended_at' => $data['discharged_at']]);
                $stay->bed->update(['state' => 'cleaning']);
            }
            $this->syncRent($admission, $data['discharged_at']);
            $invoice = $admission->invoice()->lockForUpdate()->firstOrFail();
            $due = app(BillingService::class)->totals($invoice)['due'];
            if ($due < 0) throw new DomainException('অতিরিক্ত অগ্রিম আগে রিফান্ড করুন।');
            if ($due > 0 && (auth()->user()->role !== 'admin' || empty($data['allow_due']))) throw new DomainException('বকেয়াসহ ছাড়পত্রে অ্যাডমিন অনুমোদন প্রয়োজন।');
            $admission->update(collect($data)->only(['discharged_at', 'treatment_summary', 'followup_notes', 'discharge_type'])->all() + ['status' => 'discharged']);
            $invoice->update(['status' => 'finalized']);
            Audit::record('discharge', 'admissions', $admission->id, ['due' => $due]);
        });
    }

    public function cancel(Admission $admission): void
    {
        DB::transaction(function () use ($admission) {
            $admission = Admission::lockForUpdate()->findOrFail($admission->id);
            $invoice = $admission->invoice()->lockForUpdate()->firstOrFail();
            if ($admission->status !== 'admitted' || app(BillingService::class)->totals($invoice)['paid'] != 0 || LabOrder::where('admission_id', $admission->id)->exists() || NursingRecord::where('admission_id', $admission->id)->exists()) throw new DomainException('সেবা বা আদায় থাকলে ভর্তি বাতিল নয়; ডিসচার্জ করুন।');
            $invoice->items()->delete();
            foreach ($admission->stays()->whereNull('ended_at')->get() as $stay) {
                $stay->update(['ended_at' => now()]);
                $stay->bed->update(['state' => 'available']);
            }
            $admission->update(['status' => 'cancelled']);
            $invoice->update(['status' => 'void']);
            Audit::record('cancel', 'admissions', $admission->id);
        });
    }
}
