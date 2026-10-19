<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Picker এর dd-mm-yyyy কে Y-m-d এ রূপান্তর (validation এর আগে)
    protected function prepareForValidation(): void
    {
        $this->merge(['birth_date' => date_to_db($this->input('birth_date'))]);
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:150'],
            'reference_id'      => ['nullable', 'integer', Rule::exists('reference_persons', 'id')],
            'birth_date'        => ['nullable', 'date', 'before_or_equal:today', 'required_without:age_years'],
            'age_years'         => ['nullable', 'integer', 'between:0,130', 'required_without:birth_date'],
            'gender'            => ['required', Rule::in(['female', 'male', 'other'])],
            'phone'             => ['required', 'string', 'max:150'],
            'address'           => ['nullable', 'string', 'max:10000'],
            'emergency_contact' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'              => 'রোগীর নাম',
            'reference_id'      => 'রেফারেন্স',
            'birth_date'        => 'জন্মতারিখ',
            'age_years'         => 'বয়স',
            'gender'            => 'লিঙ্গ',
            'phone'             => 'মোবাইল',
            'address'           => 'ঠিকানা',
            'emergency_contact' => 'জরুরি যোগাযোগ',
        ];
    }
}
