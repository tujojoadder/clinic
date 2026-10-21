<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can('expenses', true);
    }
    protected function prepareForValidation(): void
    {
        $this->merge(['expense_date' => date_to_db($this->input('expense_date'))]);
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => ['required', 'integer', Rule::exists('expense_categories', 'id')],
            'description'         => ['required', 'string', 'max:255'],
            'amount'              => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'account_id'          => ['required', 'integer', Rule::exists('accounts', 'id')],
            'expense_date'        => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function attributes(): array
    {
        return [
            'expense_category_id' => 'খাত',
            'description'         => 'বিবরণ',
            'amount'              => 'পরিমাণ',
            'account_id'          => 'অ্যাকাউন্ট',
            'expense_date'        => 'তারিখ',
        ];
    }
}
