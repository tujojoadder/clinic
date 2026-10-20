<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NursingRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('nursing_records', true);
    }

    // Picker এর dd-mm-yyyy hh:mm AM/PM কে Y-m-d H:i:s এ রূপান্তর (validation এর আগে)
    protected function prepareForValidation(): void
    {
        if ($this->has('recorded_at')) {
            $this->merge(['recorded_at' => datetime_to_db($this->input('recorded_at'))]);
        }
    }

    public function rules(): array
    {
        return [
            'admission_id'   => ['required', 'integer', Rule::exists('admissions', 'id')],
            'recorded_at'    => ['required', 'date_format:Y-m-d H:i:s'],
            'blood_pressure' => ['nullable', 'string', 'max:30'],
            'temperature'    => ['nullable', 'numeric', 'between:0,200'],
            'pulse'          => ['nullable', 'integer', 'between:0,300'],
            'notes'          => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'admission_id'   => 'ভর্তি নম্বর',
            'recorded_at'    => 'পর্যবেক্ষণের সময়',
            'blood_pressure' => 'রক্তচাপ (mmHg)',
            'temperature'    => 'তাপমাত্রা (°F)',
            'pulse'          => 'পালস',
            'notes'          => 'নার্সিং নোট',
        ];
    }
    public function messages(): array
    {
        return [
            'recorded_at.date_format' => ':attribute সঠিক ফরম্যাটে দিন (যেমন: 15-10-2026 02:30 PM)।',
        ];
    }
}
