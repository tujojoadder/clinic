<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestParameter extends Model
{
    protected $table = 'test_parameters';
    public $timestamps = false;
    protected $fillable = [
        'test_id',
        'name',
        'unit',
        'reference_range',
        'sort_order',
    ];
    protected $casts = [];

    public function test()
    {
        return $this->belongsTo(DiagnosticTest::class, 'test_id');
    }
}
