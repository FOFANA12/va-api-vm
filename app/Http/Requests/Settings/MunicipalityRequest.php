<?php

namespace App\Http\Requests\Settings;

use App\Models\Department;
use App\Models\Municipality;
use Illuminate\Foundation\Http\FormRequest;

class MunicipalityRequest extends FormRequest
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
            'latitude' => 'bail|nullable|numeric',
            'longitude' => 'bail|nullable|numeric',
            'department' => 'bail|required|exists:' . Department::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $municipality = $this->route('municipality');

            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Municipality::tableName() . ',name,' . $municipality->id,

            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Municipality::tableName() . ',name',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/municipality.request.name'),
            'latitude' => __('app/settings/municipality.request.latitude'),
            'longitude' => __('app/settings/municipality.request.longitude'),
            'department' => __('app/settings/municipality.request.department'),
        ];
    }
}
