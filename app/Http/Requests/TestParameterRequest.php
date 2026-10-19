<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('test_parameters', true);
    }

    public function rules(): array
    {
        return [
            'test_id'         => ['required', 'integer', Rule::exists('tests', 'id')],
            'name'            => [
                'required',
                'string',
                'max:150',
                Rule::unique('test_parameters', 'name')
                    ->where('test_id', $this->input('test_id'))
                    ->ignore($this->route('test_parameter')),
            ],
            'unit'            => ['nullable', 'string', 'max:60'],
            'reference_range' => ['nullable', 'string', 'max:150'],
            'sort_order'      => ['required', 'integer', 'between:0,1000000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'test_id'         => 'টেস্ট',
            'name'            => 'প্যারামিটার',
            'unit'            => 'ইউনিট',
            'reference_range' => 'যাচাইকৃত রেফারেন্স রেঞ্জ',
            'sort_order'      => 'ক্রম',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'এই টেস্টে এই প্যারামিটার আগে থেকেই আছে।',
        ];
    }
}
