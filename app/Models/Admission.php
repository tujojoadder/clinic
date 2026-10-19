<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $table = 'admissions';
    public $timestamps = false;
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'admitted_at',
        'reason',
        'status',
        'discharged_at',
        'treatment_summary',
        'followup_notes',
        'discharge_type',
        'created_at',
    ];
    protected $casts = [
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function stays()
    {
        return $this->hasMany(BedStay::class, 'admission_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'admission_id');
    }
}
