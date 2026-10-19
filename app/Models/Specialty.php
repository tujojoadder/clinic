<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $table = 'specialties';
    public $timestamps = false;
    protected $fillable = ['name', 'created_at'];

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'specialty_id');
    }
}
