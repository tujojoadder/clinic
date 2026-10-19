<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LabOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('lab_orders', true);
    }

    public function rules(): array
    {
        return [
            'patient_id'   => ['required', 'integer', Rule::exists('patients', 'id')],
            'admission_id' => ['nullable', 'integer', Rule::exists('admissions', 'id')],
            'test_id'      => ['required', 'integer', Rule::exists('tests', 'id')],
            // jQuery datetimepicker পাঠায়: 15-10-2026 02:30 PM
            'ordered_at'   => ['required', 'date_format:d-m-Y h:i A'],
        ];
    }

    /**
     * Picker এর dd-mm-yyyy ফরম্যাটকে ডাটাবেসের Y-m-d H:i:s এ রূপান্তর করে।
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!empty($data['ordered_at'])) {
            $data['ordered_at'] = Carbon::createFromFormat('d-m-Y h:i A', $data['ordered_at'])
                ->format('Y-m-d H:i:s');
        }

        return data_get($data, $key, $default);
    }

    public function attributes(): array
    {
        return [
            'patient_id'   => 'রোগী',
            'admission_id' => 'ভর্তি নম্বর',
            'test_id'      => 'টেস্ট',
            'ordered_at'   => 'অর্ডারের সময়',
        ];
    }

    public function messages(): array
    {
        return [
            'ordered_at.date_format' => ':attribute সঠিক ফরম্যাটে দিন (যেমন: 15-10-2026 02:30 PM)।',
        ];
    }
}
