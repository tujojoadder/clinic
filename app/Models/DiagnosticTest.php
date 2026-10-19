<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticTest extends Model
{
    protected $table = 'tests';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'sample_type',
        'price',
        'turnaround_hours',
        'active',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function parameters()
    {
        return $this->hasMany(TestParameter::class, 'test_id');
    }
}
