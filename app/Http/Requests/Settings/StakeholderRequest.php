<?php

namespace App\Http\Requests\Settings;
use App\Models\Stakeholder;
use Illuminate\Foundation\Http\FormRequest;

class StakeholderRequest extends FormRequest
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
            'name'  => 'bail|required|string|max:100|unique:' . Stakeholder::tableName() . ',name',
            'email' => 'bail|nullable|string|email|max:100',
            'phone' => 'bail|nullable|string|max:20',
        ];

        if ($this->isMethod('put')) {
            $stakeholder = $this->route('stakeholder');
            $stakeholderId = $stakeholder->id;

            $rules['name']  = 'bail|required|string|max:100|unique:' . Stakeholder::tableName() . ',name,' . $stakeholderId;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/stakeholder.request.name'),
        ];
    }
}
