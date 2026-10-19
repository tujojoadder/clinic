<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'birth_date',
        'age_years',
        'gender',
        'phone',
        'address',
        'reference_id',
        'emergency_contact',
        'created_at',
        'updated_at',
    ];
    protected $casts = [];

    public function visits()
    {
        return $this->hasMany(Visit::class, 'patient_id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class, 'patient_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'patient_id');
    }

    public function labOrders()
    {
        return $this->hasMany(LabOrder::class, 'patient_id');
    }
    public function reference()
    {
        return $this->belongsTo(ReferencePerson::class, 'reference_id');
    }
}
