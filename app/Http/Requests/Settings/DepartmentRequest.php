<?php

namespace App\Http\Requests\Settings;

use App\Models\Department;
use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'region' => 'bail|required|exists:' . Region::tableName() . ',uuid',

        ];

        if ($this->isMethod('put')) {
            $department = $this->route('department');
            
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Department::tableName() . ',name,' . $department->id,

            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Department::tableName() . ',name',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/edepartment.request.name'),
            'latitude' => __('app/settings/department.request.latitude'),
            'longitude' => __('app/settings/department.request.longitude'),
            'region' => __('app/settings/department.request.region'),
        ];
    }
}
