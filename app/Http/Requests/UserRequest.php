<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('users', true);
    }

    public function rules(): array
    {
        $editing = (bool) $this->route('user');

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => [$editing ? 'nullable' : 'required', 'string', 'min:12', 'max:128'],
            'role'     => ['required', Rule::in(['admin', 'reception', 'doctor', 'nurse', 'lab', 'cashier'])],
            'active'   => ['required', Rule::in(['1', '0'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'নাম',
            'email'    => 'ইমেইল',
            'password' => 'পাসওয়ার্ড',
            'role'     => 'রোল',
            'active'   => 'সক্রিয়',
        ];
    }
}
