<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('specialties', true);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('specialties', 'name')->ignore($this->route('specialty'))],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'বিভাগের নাম'];
    }
}
