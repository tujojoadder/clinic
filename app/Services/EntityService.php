<?php

namespace App\Services;

use App\Models\{Admission, Bed, Doctor, DoctorLedger, Invoice, NursingRecord, Visit};
use App\Support\{Audit, Registry};
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{DB, Hash};

class EntityService
{
    public function save(string $module, array $data, ?Model $record = null): Model
    {
        return DB::transaction(function () use ($module, $data, $record) {
            if ($module === 'admissions' && !$record) return app(AdmissionService::class)->create($data);
            if ($module === 'lab_orders') {
                if ($record) throw new DomainException('টেস্ট পরিচয় বদলাবেন না; রিপোর্ট/স্ট্যাটাস পরিবর্তন করুন।');
                return app(LabService::class)->create($data);
            }
            if ($record) $record = $record->newQuery()->lockForUpdate()->findOrFail($record->getKey());
            $before = $record?->toArray() ?? [];
            foreach ($data as $key => $value) {
                if ($value && str_ends_with($key, '_at')) $data[$key] = \Carbon\Carbon::parse($value)->toDateTimeString();
            }
            if ($module === 'users') {
                if ($record?->id === auth()->id() && ($data['role'] !== 'admin' || !$data['active'])) throw new DomainException('নিজের অ্যাডমিন অ্যাক্সেস বন্ধ নয়।');
                if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
                else unset($data['password']);
            }
            if ($module === 'beds' && $record && $record->stays()->whereNull('ended_at')->exists() && $data['state'] !== 'available') throw new DomainException('দখল বেডের অবস্থা পরিবর্তন নয়।');
            if ($module === 'visits') {
                if (auth()->user()->role === 'doctor') throw new DomainException('ডাক্তার শুধু সিরিয়াল সম্পন্ন করবেন।');
                $doctor = Doctor::lockForUpdate()->findOrFail($data['doctor_id']);
                if (!$doctor->active) throw new DomainException('ডাক্তার নিষ্ক্রিয়।');
                if (Visit::where('doctor_id', $doctor->id)->whereDate('appointment_at', $data['appointment_at'])->where('serial_no', $data['serial_no'])->where('id', '!=', $record?->id ?? 0)->where('status', '!=', 'cancelled')->exists()) throw new DomainException('এই দিনে ডাক্তারের সিরিয়াল আছে।');
                if ($record) {
                    $invoice = $record->invoice;
                    $paid = app(BillingService::class)->totals($invoice)['paid'];
                    if (($paid > 0 || $invoice->status !== 'open') && ($record->patient_id != $data['patient_id'] || $record->fee != $data['fee'])) throw new DomainException('পেমেন্ট/চূড়ান্ত বিলের পরে রোগী/ফি পরিবর্তন নয়।');
                    if ($data['status'] === 'cancelled' && $paid > 0) throw new DomainException('আগে পেমেন্ট নিষ্পত্তি করুন।');
                }
            }
            if ($module === 'admissions' && $record && ($record->status !== 'admitted' || $record->patient_id != $data['patient_id'] || strtotime($record->admitted_at) !== strtotime($data['admitted_at']))) throw new DomainException('ভর্তির পরিচয়/সময় পরিবর্তন নয়।');
            if ($module === 'nursing_records') {
                if (Admission::findOrFail($data['admission_id'])->status !== 'admitted') throw new DomainException('রোগী বর্তমানে ভর্তি নেই।');
                if (!$record) $data['user_id'] = auth()->id();
            }
            if ($module === 'invoices') {
                if (!$record && $data['discount'] > 0) throw new DomainException('চার্জ যোগ করার পরে ছাড় দিন।');
                if ($record) {
                    if ($record->status !== 'open') throw new DomainException('চূড়ান্ত বিল সম্পাদনা নয়।');
                    $totals = app(BillingService::class)->totals($record);
                    if ($record->visit_id || $record->admission_id || $totals['paid'] > 0) $data['patient_id'] = $record->patient_id;
                    if ($data['discount'] > $totals['subtotal']) throw new DomainException('ছাড় চার্জের বেশি নয়।');
                    if ($data['discount'] != $record->discount && auth()->user()->role !== 'admin') throw new DomainException('ছাড় পরিবর্তনে অ্যাডমিন প্রয়োজন।');
                }
            }
            if ($module === 'doctor_ledgers' && $data['type'] === 'settlement') {
                Doctor::lockForUpdate()->findOrFail($data['doctor_id']);
                $balance = (float) DoctorLedger::where('doctor_id', $data['doctor_id'])->where('id', '!=', $record?->id ?? 0)->selectRaw("COALESCE(SUM(CASE WHEN type='payable' THEN amount ELSE -amount END), 0) AS balance")->value('balance');
                if ($data['amount'] > $balance) throw new DomainException('প্রাপ্যের বেশি নিষ্পত্তি নয়।');
            }
            $isNew = !$record;
            $record ??= Registry::model($module);
            $record->fill($data)->save();
            if ($module === 'visits') {
                if ($isNew) {
                    $invoice = Invoice::create([
    'patient_id' => $record->patient_id,
    'visit_id' => $record->id,
    'discount' => 0,
    'status' => 'open',
]);
                    app(BillingService::class)->addItem($invoice, ['description' => 'ডাক্তার ভিজিট', 'quantity' => 1, 'unit_price' => $record->fee, 'kind' => 'visit']);
                } else {
                    $invoice = $record->invoice;
                    $invoice->items()->where('kind', 'visit')->update(['unit_price' => $record->fee]);
                }
                $invoice->update(['patient_id' => $record->patient_id, 'status' => $record->status === 'cancelled' ? 'void' : $invoice->status]);
            }
            Audit::record($isNew ? 'create' : 'update', $record->getTable(), $record->id, ['before' => $before, 'after' => $record->toArray()]);
            return $record;
        });
    }

    public function delete(string $module, Model $record): void
    {
        if (in_array($module, ['visits', 'admissions', 'invoices', 'lab_orders'])) throw new DomainException('সেবার রেকর্ড মুছবেন না; cancel/void ব্যবহার করুন।');
        if ($module === 'users' && $record->id === auth()->id()) throw new DomainException('নিজের ইউজার মোছা যাবে না।');
        DB::transaction(function () use ($record) {
            $id = $record->id; $table = $record->getTable(); $snapshot = $record->toArray();
            $record->delete();
            Audit::record('delete', $table, $id, $snapshot);
        });
    }
}
