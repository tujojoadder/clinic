<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorLedger extends Model
{
    protected $table = 'doctor_ledgers';
    public $timestamps = false;
    protected $fillable = [
        'doctor_id',
        'type',
        'amount',
        'entry_date',
        'notes',
        'created_at',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
