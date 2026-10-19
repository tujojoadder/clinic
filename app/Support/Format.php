<?php
namespace App\Support;
final class Format
{
    public static function money($value): string { return '৳ '.number_format((float) $value, 2); }
    public static function value($value, array $field)
    {
        if ($field[1] === 'fk' && $value) return Registry::model($field[3])->find($value)?->name ?? '#'.$value;
        if ($field[1] === 'select') return $field[3][$value] ?? $value;
        return $value;
    }
}
