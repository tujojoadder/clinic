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

    public function rules(): array
    {
        return [
            'category'     => ['required', 'string', 'max:100'],
            'description'  => ['required', 'string', 'max:255'],
            'amount'       => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'method'       => ['required', Rule::in(['cash', 'bkash', 'nagad', 'bank'])],
            'expense_date' => ['required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category'     => 'খাত',
            'description'  => 'বিবরণ',
            'amount'       => 'পরিমাণ',
            'method'       => 'পদ্ধতি',
            'expense_date' => 'তারিখ',
        ];
    }
}
