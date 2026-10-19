<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $table = 'lab_orders';
    public $timestamps = false;
    protected $fillable = [
        'patient_id',
        'admission_id',
        'invoice_id',
        'test_id',
        'status',
        'ordered_at',
        'collected_at',
        'approved_at',
        'approved_by',
        'report_notes',
    ];
    protected $casts = [
        'ordered_at' => 'datetime',
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function test()
    {
        return $this->belongsTo(DiagnosticTest::class, 'test_id');
    }

    public function results()
    {
        return $this->hasMany(LabResult::class, 'order_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
