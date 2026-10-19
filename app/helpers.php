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
    // 2026-10-15 → 15-10-2026 (blade এর জন্য)
    function date_to_view($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : '';
    }
}

if (!function_exists('datetime_to_view')) {
    // 2026-10-15 14:30:00 → 15-10-2026 02:30 PM
    function datetime_to_view($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y h:i A') : '';
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
