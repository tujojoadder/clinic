<?php

namespace App\Services;

use App\Models\{Doctor, Invoice, Visit};
use App\Support\Audit;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class VisitService
{
    public function save(array $data, ?Visit $visit = null): Visit
    {
        return DB::transaction(function () use ($data, $visit) {
            if (auth()->user()->role === 'doctor') throw new DomainException('ডাক্তার শুধু সিরিয়াল সম্পন্ন করবেন।');
            if ($visit) $visit = Visit::lockForUpdate()->findOrFail($visit->id);
            $before = $visit?->toArray() ?? [];
            $appointment = Carbon::parse($data['appointment_at']);
            $data['appointment_at'] = $appointment->toDateTimeString();

            $doctor = Doctor::lockForUpdate()->findOrFail($data['doctor_id']);
            if (!$doctor->active) throw new DomainException('ডাক্তার নিষ্ক্রিয়।');
            // নতুন ভিজিট বা ডাক্তার/দিন বদলালে: ওই দিনে ডাক্তার বসেন কিনা যাচাই
            if (!$visit || $visit->doctor_id != $doctor->id || Carbon::parse($visit->appointment_at)->toDateString() !== $appointment->toDateString()) {
                $weekday = ($appointment->dayOfWeek + 1) % 7; // শনি=0 ... শুক্র=6
                if ($doctor->days()->exists() && !$doctor->days()->where('days.weekday', $weekday)->exists()) {
                    throw new DomainException('এই দিনে ডাক্তার বসেন না।');
                }
            }
            // সিরিয়াল স্বয়ংক্রিয়: নতুন ভিজিট বা ডাক্তার/দিন বদলালে পরের নম্বর, নইলে আগেরটাই
            if (!$visit || $visit->doctor_id != $doctor->id || Carbon::parse($visit->appointment_at)->toDateString() !== $appointment->toDateString()) {
                $data['serial_no'] = (Visit::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_at', $appointment->toDateString())
                    ->max('serial_no') ?? 0) + 1;
            } else {
                $data['serial_no'] = $visit->serial_no;
            }
            if (Visit::where('doctor_id', $doctor->id)->whereDate('appointment_at', $appointment->toDateString())->where('serial_no', $data['serial_no'])->where('id', '!=', $visit?->id ?? 0)->where('status', '!=', 'cancelled')->exists()) {
                throw new DomainException('এই দিনে ডাক্তারের সিরিয়াল আছে।');
            }
            if ($visit) {
                $invoice = $visit->invoice;
                $paid = app(BillingService::class)->totals($invoice)['paid'];
                if (($paid > 0 || $invoice->status !== 'open') && ($visit->patient_id != $data['patient_id'] || $visit->fee != $data['fee'])) {
                    throw new DomainException('পেমেন্ট/চূড়ান্ত বিলের পরে রোগী/ফি পরিবর্তন নয়।');
                }
                if ($data['status'] === 'cancelled' && $paid > 0) throw new DomainException('আগে পেমেন্ট নিষ্পত্তি করুন।');
            }

            $isNew = !$visit;
            $visit ??= new Visit;
            $visit->fill($data)->save();

            if ($isNew) {
                $invoice = Invoice::create([
                    'patient_id' => $visit->patient_id,
                    'visit_id' => $visit->id,
                    'discount' => 0,
                    'status' => 'open',
                ]);
                app(BillingService::class)->addItem($invoice, ['description' => 'ডাক্তার ভিজিট', 'quantity' => 1, 'unit_price' => $visit->fee, 'kind' => 'visit']);
            } else {
                $invoice = $visit->invoice;
                $invoice->items()->where('kind', 'visit')->update(['unit_price' => $visit->fee]);
            }
            $invoice->update(['patient_id' => $visit->patient_id, 'status' => $visit->status === 'cancelled' ? 'void' : $invoice->status]);

            Audit::record($isNew ? 'create' : 'update', $visit->getTable(), $visit->id, ['before' => $before, 'after' => $visit->toArray()]);

            return $visit;
        });
    }
}
