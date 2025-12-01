<?php

namespace App\Http\Requests\Settings;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:' . Permission::tableName() . ',id',
        ];

        if ($this->isMethod('put')) {
            $role = $this->route('role');
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Role::tableName() . ',name,' . $role->id,
            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Role::tableName() . ',name',
            ];
        }

        return $rules;
    }

    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        return [
            'name' => __('app/settings/role.request.name'),
            'permissions' => __('app/settings/role.request.permissions'),
        ];
    }
}
