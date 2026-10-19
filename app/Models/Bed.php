<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    protected $table = 'beds';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'ward',
        'type',
        'daily_rate',
        'state',
        'created_at',
    ];
    protected $casts = [
        'daily_rate' => 'decimal:2',
    ];

    public function stays()
    {
        return $this->hasMany(BedStay::class, 'bed_id');
    }
}
