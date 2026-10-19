<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('doctors', true);
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:150'],
            'specialty_id'   => ['required', 'integer', 'exists:specialties,id'],
            'phone'          => ['nullable', 'string', 'max:150'],
            'fee'            => ['required', 'numeric', 'min:0'],
            'followup_fee'   => ['required', 'numeric', 'min:0'],
            'days'           => ['nullable', 'array'],
            'days.*'         => ['integer', 'exists:days,id'],
            'start_time'     => ['nullable', 'date_format:H:i'],
            'end_time'       => ['nullable', 'date_format:H:i'],
            'active'         => ['required', Rule::in(['1', '0'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => 'নাম',
            'specialty_id'   => 'বিভাগ',
            'phone'          => 'মোবাইল',
            'fee'            => 'ভিজিট ফি',
            'followup_fee'   => 'ফলোআপ ফি',
            'days'           => 'কোন দিন বসেন',
            'days.*'         => 'বসার দিন',
            'start_time'     => 'শুরুর সময়',
            'end_time'       => 'শেষ সময়',
            'active'         => 'সক্রিয়',
        ];
    }
}
