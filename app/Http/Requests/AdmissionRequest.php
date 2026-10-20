<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('admissions', true);
    }

    // Picker এর dd-mm-yyyy hh:mm AM/PM কে Y-m-d H:i:s এ রূপান্তর (validation এর আগে)
    protected function prepareForValidation(): void
    {
        if (!$this->route('admission') && $this->has('admitted_at')) {
            $this->merge(['admitted_at' => datetime_to_db($this->input('admitted_at'))]);
        }
    }

    public function rules(): array
    {
        if ($this->route('admission')) {
            return [
                'doctor_id' => ['required', 'integer', Rule::exists('doctors', 'id')],
                'reason'    => ['required', 'string', 'max:10000'],
            ];
        }

        return [
            'patient_id'  => ['required', 'integer', Rule::exists('patients', 'id')],
            'doctor_id'   => ['required', 'integer', Rule::exists('doctors', 'id')],
            'admitted_at' => ['required', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'],
            'reason'      => ['required', 'string', 'max:10000'],
            'bed_id'      => ['required', 'integer', Rule::exists('beds', 'id')],
            'advance'     => ['nullable', 'numeric', 'min:0'],
            'method'      => ['required', Rule::in(['cash', 'bkash', 'nagad', 'bank'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id'  => 'রোগী',
            'doctor_id'   => 'ডাক্তার',
            'admitted_at' => 'ভর্তির তারিখ ও সময়',
            'reason'      => 'ভর্তির কারণ',
            'bed_id'      => 'বেড',
            'advance'     => 'অগ্রিম',
            'method'      => 'পদ্ধতি',
        ];
    }

    public function messages(): array
    {
        return [
            'admitted_at.date_format' => ':attribute সঠিক ফরম্যাটে দিন (যেমন: 15-10-2026 02:30 PM)।',
        ];
    }
}
