<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;

class ReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('references', true);
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:150'],
            'phone'              => ['required', 'string', 'max:30'],
            'commission_percent' => ['required', 'numeric', 'between:0,100'],
            'joining_date'       => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'               => 'নাম',
            'phone'              => 'মোবাইল',
            'commission_percent' => 'কমিশন (%)',
            'joining_date'       => 'জয়েনিং ডেট',
        ];
    }
}
