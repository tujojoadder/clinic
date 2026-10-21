<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    public $timestamps = false;
    protected $fillable = [
        'category',
        'description',
        'amount',
        'account_id',
        'expense_date',
        'created_at',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
