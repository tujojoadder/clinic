<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferencePerson extends Model
{
    protected $table = 'reference_persons';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'phone',
        'commission_percent',
        'joining_date',
        'created_at',
    ];
    protected $casts = [
        'commission_percent' => 'decimal:2',
    ];
}
