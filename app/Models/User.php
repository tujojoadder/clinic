<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'role',
        'active',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $hidden = ['password', 'remember_token'];
}
