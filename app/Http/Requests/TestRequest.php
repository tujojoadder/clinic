<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('tests', true);
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:150', Rule::unique('tests', 'name')->ignore($this->route('test'))],
            'sample_type'      => ['required', 'string', 'max:80'],
            'price'            => ['required', 'numeric', 'min:0', 'max:999999999'],
            'turnaround_hours' => ['required', 'integer', 'between:0,65535'],
            'active'           => ['required', Rule::in(['1', '0'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'             => 'টেস্টের নাম',
            'sample_type'      => 'নমুনা',
            'price'            => 'মূল্য',
            'turnaround_hours' => 'সময় (ঘণ্টা)',
            'active'           => 'সক্রিয়',
        ];
    }
}
