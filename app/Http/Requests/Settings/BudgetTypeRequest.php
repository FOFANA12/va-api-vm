<?php

namespace App\Http\Requests\Settings;

use App\Models\BudgetType;
use Illuminate\Foundation\Http\FormRequest;

class BudgetTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name'  => 'bail|required|string|max:50|unique:' . BudgetType::tableName() . ',name',
        ];

        if ($this->isMethod('put')) {
            $budgetType = $this->route('budget_type');

            $rules['name']  = 'bail|required|string|max:50|unique:' . BudgetType::tableName() . ',name,' . $budgetType->id;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/budget_type.request.name'),
        ];
    }
}
