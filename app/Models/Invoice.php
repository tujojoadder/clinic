<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    public $timestamps = false;
    protected $fillable = [
        'patient_id',
        'visit_id',
        'admission_id',
        'discount',
        'status',
        'notes',
        'created_at',
    ];
    protected $casts = [
        'discount' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
