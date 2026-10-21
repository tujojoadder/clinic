<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


//  Model

// Payment, Expense: fillable-এ account_id ও account() relation
// Account: payments(), expenses()

// Form (যেখানে account select করা হয়)

// expenses/form: খরচ লেখার সময়
// admissions/form: ভর্তির advance
// billing/detail: invoice-এ payment নেওয়ার ফর্ম

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'holder_name',
        'account_number',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'account_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'account_id');
    }
}
