<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'visits';
    public $timestamps = false;
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'reference_id',
        'appointment_at',
        'serial_no',
        'visit_type',
        'fee',
        'status',
        'notes',
        'created_at',
    ];
    protected $casts = [
        'fee'            => 'decimal:2',
        'appointment_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
    public function reference()
    {
        return $this->belongsTo(ReferencePerson::class, 'reference_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'visit_id');
    }
}
