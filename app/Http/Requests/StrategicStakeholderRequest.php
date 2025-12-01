<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StrategicStakeholderRequest extends FormRequest
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
        return [
            'organization' => 'bail|required|max:50',
            'responsible' => 'bail|required|max:50',
            'email' => 'bail|required|email|max:100',
            'phone' => 'bail|required|max:20',
        ];
    }



    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        return [
            'organization' => __('app/strategic_stakeholder.request.organization'),
            'responsible' => __('app/strategic_stakeholder.request.responsible'),
            'email' => __('app/strategic_stakeholder.request.email'),
            'phone' => __('app/strategic_stakeholder.request.phone'),
        ];
    }
}
