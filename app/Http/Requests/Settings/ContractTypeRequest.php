<?php

namespace App\Http\Requests\Settings;

use App\Models\ContractType;
use Illuminate\Foundation\Http\FormRequest;

class ContractTypeRequest extends FormRequest
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
            $contractType = $this->route('contract_type');

            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . ContractType::tableName() . ',name,' . $contractType->id,

            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . ContractType::tableName() . ',name',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/contract_type.request.name'),
        ];
    }
}
