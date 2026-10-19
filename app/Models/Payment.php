<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    public $timestamps = false;
    protected $fillable = [
        'invoice_id',
        'amount',
        'type',
        'method',
        'transaction_ref',
        'reason',
        'paid_at',
        'user_id',
        'voided_at',
        'void_reason',
        'created_at',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
