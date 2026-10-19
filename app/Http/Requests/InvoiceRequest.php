<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('invoices', true);
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', Rule::exists('patients', 'id')],
            'discount'   => ['required', 'numeric', 'min:0', 'max:999999999'],
            'notes'      => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id' => 'রোগী',
            'discount'   => 'ছাড়',
            'notes'      => 'বিলের নোট',
        ];
    }
}
