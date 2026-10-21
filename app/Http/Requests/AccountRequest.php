<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('accounts', true);
    }

    public function rules(): array
    {
        $id = $this->route('account'); // edit এর সময় নিজেকে বাদ দিতে

        return [
            'name'           => ['required', 'string', 'max:150'],
            'holder_name'    => ['required', 'string', 'max:150'],
            'account_number' => ['required', 'string', 'max:50', Rule::unique('accounts', 'account_number')->ignore($id)],
            'is_default'     => ['nullable', 'in:0,1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => 'অ্যাকাউন্টের নাম',
            'holder_name'    => 'হোল্ডারের নাম',
            'account_number' => 'অ্যাকাউন্ট নম্বর',
        ];
    }
}
