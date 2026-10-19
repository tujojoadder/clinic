<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $table = 'login_attempts';
    public $timestamps = false;
    protected $fillable = [
        'email',
        'ip',
        'occurred_at',
    ];
    protected $casts = [
    ];
}
