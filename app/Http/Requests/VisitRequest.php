<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('visits', true);
    }

    // Picker এর dd-mm-yyyy কে Y-m-d H:i:s এ রূপান্তর (validation এর আগে)
    protected function prepareForValidation(): void
    {
        $this->merge(['appointment_at' => datetime_to_db($this->input('appointment_at'))]);
    }

    public function rules(): array
    {
        return [
            'patient_id'     => ['required', 'integer', Rule::exists('patients', 'id')],
            'doctor_id'      => ['required', 'integer', Rule::exists('doctors', 'id')],
            // prepareForValidation এর পর: 2026-10-15 14:30:00
            'appointment_at' => ['required', 'date_format:Y-m-d H:i:s'],

            'visit_type'     => ['required', Rule::in(['new', 'followup'])],
            'fee'            => ['required', 'numeric', 'min:0'],
            'status'         => ['required', Rule::in(['waiting', 'completed', 'cancelled'])],
            'notes'          => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id'     => 'রোগী',
            'doctor_id'      => 'ডাক্তার',

            'appointment_at' => 'তারিখ ও সময়',

            'visit_type'     => 'ভিজিট',
            'fee'            => 'ভিজিট ফি',
            'status'         => 'স্ট্যাটাস',
            'notes'          => 'নোট',
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_at.date_format' => ':attribute সঠিক ফরম্যাটে দিন (যেমন: 15-10-2026 02:30 PM)।',
        ];
    }
}
