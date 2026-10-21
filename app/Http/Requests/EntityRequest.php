<?php

namespace App\Http\Requests;

use App\Support\Registry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Registry::can((string) $this->route('module'), true);
    }
    // Picker এর dd-mm-yyyy কে ডাটাবেস ফরম্যাটে রূপান্তর (validation এর আগে)
    protected function prepareForValidation(): void
    {
        $definition = Registry::definition((string) $this->route('module'));
        $merge = [];
        foreach ($definition['fields'] as $key => $field) {
            if (!$this->has($key)) continue;
            if ($field[1] === 'date') $merge[$key] = date_to_db($this->input($key));
            elseif ($field[1] === 'datetime-local') $merge[$key] = datetime_to_db($this->input($key));
        }
        $this->merge($merge);
    }
    public function rules(): array
    {
        $module = (string) $this->route('module');
        $definition = Registry::definition($module);
        $id = (int) $this->route('record');
        $rules = [];
        foreach ($definition['fields'] as $key => $field) {
            $rules[$key] = [$field[2] ? 'required' : 'nullable'];
            switch ($field[1]) {
                case 'fk':
                    $rules[$key] = array_merge($rules[$key], ['integer', Rule::exists($field[3], 'id')]);
                    break;
                case 'select':
                    $rules[$key][] = Rule::in(array_keys($field[3]));
                    break;
                case 'number':
                    $rules[$key] = array_merge($rules[$key], ['numeric', 'min:0']);
                    break;
                case 'date':
                case 'datetime-local':
                    $rules[$key][] = 'date';
                    break;
                case 'time':
                    $rules[$key][] = 'date_format:H:i';
                    break;
                case 'email':
                    $rules[$key] = array_merge($rules[$key], ['email', 'max:190']);
                    break;
                case 'password':
                    $rules[$key] = [$id ? 'nullable' : 'required', 'string', 'min:12', 'max:128'];
                    break;
                default:
                    $rules[$key] = array_merge($rules[$key], ['string', 'max:' . ($field[1] === 'textarea' ? 10000 : 150)]);
            }
        }
        if ($module === 'patients') {
            $rules['age_years'] = ['nullable', 'integer', 'between:0,130', 'required_without:birth_date'];
            $rules['birth_date'] = ['nullable', 'date', 'before_or_equal:today', 'required_without:age_years'];
        }
        foreach (['serial_no', 'pulse', 'sort_order', 'turnaround_hours'] as $key) {
            if (isset($rules[$key])) $rules[$key] = array_merge($rules[$key], ['integer']);
        }
        if ($module === 'visits') $rules['serial_no'][] = 'min:1';
        if (in_array($module, ['expenses', 'doctor_ledgers'])) $rules['amount'][] = 'gt:0';
        if ($module === 'users') $rules['email'][] = Rule::unique('users', 'email')->ignore($id);
        if (in_array($module, ['beds', 'tests'])) $rules['name'][] = Rule::unique($definition['table'], 'name')->ignore($id);
        if ($module === 'test_parameters') $rules['name'][] = Rule::unique('test_parameters', 'name')->where('test_id', $this->input('test_id'))->ignore($id);
        if ($module === 'admissions') {
            $rules['admitted_at'][] = 'before_or_equal:now';
            if (!$id) {
                $rules['bed_id'] = ['required', 'integer', 'exists:beds,id'];
                $rules['advance'] = ['nullable', 'numeric', 'min:0'];
                $rules['account_id'] = ['required', 'integer', 'exists:accounts,id'];
            }
        }
        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        foreach (Registry::definition((string) $this->route('module'))['fields'] as $key => $field) $attributes[$key] = $field[0];
        return $attributes;
    }
}
