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
            'admitted_at' => ['required', 'date', 'before_or_equal:now'],
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
}
