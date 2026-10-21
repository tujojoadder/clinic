<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = ['name', 'holder_name', 'account_number', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];
}
