<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

final class Audit
{
    public static function record(string $action, string $entity, ?int $id = null, array $details = []): void
    {
        unset($details['password'], $details['remember_token']);
        AuditLog::create([
            'user_id' => auth()->id(), 'action' => $action, 'entity' => $entity,
            'entity_id' => $id, 'details' => json_encode($details, JSON_UNESCAPED_UNICODE),
            'ip' => app()->runningInConsole() ? 'CLI' : request()->ip(),
        ]);
    }
}
