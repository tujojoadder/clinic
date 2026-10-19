<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';
    public $timestamps = false;
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'kind',
        'bed_stay_id',
        'lab_order_id',
        'created_at',
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];
}
