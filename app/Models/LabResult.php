<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    protected $table = 'lab_results';
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'parameter_name',
        'result_value',
        'unit',
        'reference_range',
    ];
    protected $casts = [
    ];
}
