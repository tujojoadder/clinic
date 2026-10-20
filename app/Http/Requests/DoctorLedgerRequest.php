<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorLedgerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('doctor_ledgers', true);
    }

    // Picker এর dd-mm-yyyy কে Y-m-d এ রূপান্তর (validation এর আগে)
    protected function prepareForValidation(): void
    {
        $this->merge(['entry_date' => date_to_db($this->input('entry_date'))]);
    }

    public function rules(): array
    {
        return [
            'doctor_id'  => ['required', 'integer', Rule::exists('doctors', 'id')],
            'type'       => ['required', Rule::in(['payable', 'settlement'])],
            'amount'     => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'entry_date' => ['required', 'date_format:Y-m-d'],
            'notes'      => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'doctor_id'  => 'ডাক্তার',
            'type'       => 'এন্ট্রি',
            'amount'     => 'পরিমাণ',
            'entry_date' => 'তারিখ',
            'notes'      => 'বিবরণ',
        ];
    }
}
