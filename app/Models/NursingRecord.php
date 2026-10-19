<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NursingRecord extends Model
{
    protected $table = 'nursing_records';
    public $timestamps = false;
    protected $fillable = [
        'admission_id',
        'recorded_at',
        'blood_pressure',
        'temperature',
        'pulse',
        'notes',
        'user_id',
        'created_at',
    ];
    protected $casts = [];

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
