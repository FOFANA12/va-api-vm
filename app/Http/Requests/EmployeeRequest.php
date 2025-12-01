<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\Structure;
use App\Models\User;
use App\Support\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
            'name' => 'bail|required|string|max:100',
            'job_title' => 'bail|nullable|string|max:50',
            'floor' => 'bail|nullable|string|max:10',
            'office' => 'bail|nullable|string|max:20',
            'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
            'avatar' => 'bail|nullable|max:5120|mimes:jpeg,png,jpg',
            'lang' => ['bail', 'required', Rule::in(Language::codes())],
        ];

        if ($this->isMethod('put')) {
            $employee = $this->route('employee');
            $userId = $employee->user->id;

            if ($this->can_logged_in) {
                $rules += [
                    'email' => 'bail|required|string|email|max:100|unique:' . User::tableName() . ',email,' . $userId,
                    'phone' => 'bail|nullable|string|max:20|unique:' . User::tableName() . ',phone,' . $userId,
                    'role' => 'bail|required|exists:' . Role::tableName() . ',uuid',
                    'password' => 'bail|nullable|string|min:6|confirmed',
                ];
            } else {
                $rules += [
                    'email' => 'bail|nullable|string|email|max:100|unique:' . User::tableName() . ',email,' . $userId,
                    'phone' => 'bail|nullable|string|max:20|unique:' . User::tableName() . ',phone,' . $userId,
                    'password' => 'bail|nullable|string|min:6|confirmed',
                ];
            }
        } else {
            if ($this->can_logged_in) {
                $rules += [
                    'email' => 'bail|required|string|email|max:100|unique:' . User::tableName() . ',email',
                    'role' => 'bail|required|exists:' . Role::tableName() . ',uuid',
                    'phone' => 'bail|nullable|string|max:20|unique:' . User::tableName() . ',phone',
                    'password' => 'bail|required|string|min:6|confirmed',
                ];
            } else {
                $rules += [
                    'email' => 'bail|nullable|string|email|max:100|unique:' . User::tableName() . ',email',
                    'phone' => 'bail|nullable|string|max:20|unique:' . User::tableName() . ',phone',
                    'password' => 'bail|nullable|string|min:6|confirmed',
                ];
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/employee.request.name'),
            'job_title' => __('app/employee.request.job_title'),
            'floor' => __('app/employee.request.floor'),
            'office' => __('app/employee.request.office'),
            'structure' => __('app/employee.request.structure'),
            'avatar' => __('app/employee.request.avatar'),
            'role' => __('app/employee.request.role'),
            'email' => __('app/employee.request.email'),
            'phone' => __('app/employee.request.phone'),
            'password' => __('app/employee.request.password'),
            'lang' => __('app/employee.request.lang'),
        ];
    }
}
