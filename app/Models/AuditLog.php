<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'action',
        'entity',
        'entity_id',
        'details',
        'ip',
        'created_at',
    ];
    protected $casts = [
    ];
}
