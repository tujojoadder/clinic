<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

final class Registry
{
    public static function modules(): array
    {
        return config('clinic.modules');
    }
    public static function definition(string $module): array
    {
        return self::modules()[$module] ?? abort(404);
    }
    public static function model(string $module): Model
    {
        $class = self::definition($module)['model'] ?? abort(404);
        return new $class;
    }
    public static function can(string $module, bool $write = false): bool
    {
        $user = auth()->user();
        if (!$user || !$user->active) return false;
        return $user->role === 'admin' || in_array($user->role, self::definition($module)[$write ? 'write' : 'read'] ?? [], true);
    }
    public static function authorize(string $module, bool $write = false): void
    {
        abort_unless(self::can($module, $write), 403);
    }
    public static function setting(string $key, string $default = ''): string
    {
        return (string) (Setting::find($key)?->value ?? $default);
    }
    public static function options(string $module): array
    {
        return self::model($module)->latest('id')->limit(5000)->get()->mapWithKeys(fn($record) => [$record->id => ($record->name ?? 'ADM-' . $record->id)])->all();
    }
}
