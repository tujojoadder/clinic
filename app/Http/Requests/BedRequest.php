<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('beds', true);
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:80', Rule::unique('beds', 'name')->ignore($this->route('bed'))],
            'ward'       => ['required', 'string', 'max:100'],
            'type'       => ['required', Rule::in(['ward', 'cabin', 'icu'])],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'state'      => ['required', Rule::in(['available', 'cleaning', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'       => 'বেড / কেবিন নম্বর',
            'ward'       => 'ওয়ার্ড',
            'type'       => 'ধরন',
            'daily_rate' => 'প্রতি ২৪ ঘণ্টার ভাড়া',
            'state'      => 'অবস্থা',
        ];
    }
}
