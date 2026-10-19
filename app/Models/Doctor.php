<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctors';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'specialty_id',
        'phone',
        'fee',
        'followup_fee',
        'available_days',
        'start_time',
        'end_time',
        'active',
        'created_at',
    ];
    protected $casts = [
        'fee' => 'decimal:2',
        'followup_fee' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'doctor_id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class, 'doctor_id');
    }
    public function days()
    {
        return $this->belongsToMany(Day::class, 'doctor_day', 'doctor_id', 'day_id')->orderBy('days.id');
    }
}
