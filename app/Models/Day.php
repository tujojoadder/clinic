<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $table = 'days';
    public $timestamps = false;
    protected $fillable = ['name', 'weekday'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_day', 'day_id', 'doctor_id');
    }
}
