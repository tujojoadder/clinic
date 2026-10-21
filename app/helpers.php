<?php

use Carbon\Carbon;

if (!function_exists('date_to_db')) {
    // 15-10-2026 → 2026-10-15 (format না মিললে যেমন আছে তেমনই ফেরত)
    function date_to_db($value)
    {
        return $value && Carbon::hasFormat($value, 'd-m-Y')
            ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d')
            : $value;
    }
}

if (!function_exists('datetime_to_db')) {
    // 15-10-2026 02:30 PM → 2026-10-15 14:30:00
    function datetime_to_db($value)
    {
        return $value && Carbon::hasFormat($value, 'd-m-Y h:i A')
            ? Carbon::createFromFormat('d-m-Y h:i A', $value)->format('Y-m-d H:i:s')
            : $value;
    }
}

if (!function_exists('date_to_view')) {
    // 2026-10-15 → 15-10-2026 (blade এর জন্য; পার্স না হলে যেমন আছে তেমনই ফেরত)
    function date_to_view($value)
    {
        if (!$value) return '';
        try {
            return Carbon::parse($value)->format('d-m-Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}

if (!function_exists('datetime_to_view')) {
    // 2026-10-15 14:30:00 → 15-10-2026 02:30 PM (পার্স না হলে যেমন আছে তেমনই ফেরত)
    function datetime_to_view($value)
    {
        if (!$value) return '';
        try {
            return Carbon::parse($value)->format('d-m-Y h:i A');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}

if (!function_exists('doctorLabel')) {
    function doctorLabel(\App\Models\Doctor $d): string
    {
        $label = $d->name;

        if ($d->specialty) {
            $label .= ' (' . $d->specialty->name . ')';
        }

        if ($d->start_time && $d->end_time) {
            $label .= ' (' .
                \Carbon\Carbon::parse($d->start_time)->format('h:i A') .
                ' - ' .
                \Carbon\Carbon::parse($d->end_time)->format('h:i A') .
                ')';
        }

        return $label;
    }
}


if (!function_exists('per_page')) {
    // ?per_page=50 → 50 (শুধু 20/50/100 চলবে, না হলে 20)
    function per_page(int $default = 20): int
    {
        $size = (int) request('per_page', $default);

        return in_array($size, [20, 50, 100], true) ? $size : $default;
    }
}
if (!function_exists('account_options')) {
    function account_options()
    {
        return \App\Models\Account::orderByDesc('is_default')->orderBy('name')->get(['id', 'name', 'account_number', 'is_default']);
    }
}


if (!function_exists('expense_category_options')) {
    function expense_category_options()
    {
        return \App\Models\ExpenseCategory::orderBy('name')
            ->get(['id', 'name']);
    }
}
