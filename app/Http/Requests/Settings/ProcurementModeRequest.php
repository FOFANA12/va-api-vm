<?php

namespace App\Http\Requests\Settings;

use App\Models\ProcurementMode;
use App\Models\ContractType;
use Illuminate\Foundation\Http\FormRequest;

class ProcurementModeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'bail|required|max:50|unique:' . ProcurementMode::tableName() . ',name',
            'duration' => 'bail|required|integer|gte:0',
            'contract_type' => 'bail|required|uuid|exists:' . ContractType::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $mode = $this->route('procurement_mode');

            $rules['name'] = 'bail|required|max:50|unique:' . ProcurementMode::tableName() . ',name,' . $mode->id;
        }

        return $rules;
    }


    public function attributes(): array
    {
        return [
            'name' => __('app/settings/procurement_mode.request.name'),
            'duration' => __('app/settings/procurement_mode.request.duration'),
            'contract_type' => __('app/settings/procurement_mode.request.contract_type'),
        ];
    }
}
