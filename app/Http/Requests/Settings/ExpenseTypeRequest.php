<?php

namespace App\Http\Requests\Settings;

use App\Models\ExpenseType;
use App\Models\Structure;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseTypeRequest extends FormRequest
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
        $rules = [];

        if ($this->isMethod('put')) {
            $expenseType = $this->route('expense_type');
            $expenseTypesId = $expenseType->id;

            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . ExpenseType::tableName() . ',name,' . $expenseTypesId,

            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . ExpenseType::tableName() . ',name',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/expense_type.request.name'),
        ];
    }
}
