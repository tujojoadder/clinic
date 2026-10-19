<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedStay extends Model
{
    protected $table = 'bed_stays';
    public $timestamps = false;
    protected $fillable = [
        'admission_id',
        'bed_id',
        'started_at',
        'ended_at',
        'daily_rate',
    ];
    protected $casts = [
        'daily_rate' => 'decimal:2',
    ];

    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
