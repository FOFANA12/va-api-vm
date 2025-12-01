<?php

namespace App\Http\Requests\Settings;

use App\Models\Role;
use App\Models\User;
use App\Support\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'lang' => ['bail', 'required', Rule::in(Language::codes())],
            'role' => 'bail|required|exists:' . Role::tableName() . ',uuid',
            'avatar' => 'bail|nullable|max:5120|mimes:jpeg,png,jpg',
        ];

        if ($this->isMethod('put')) {
            $user = $this->route('user');
            $rules += [
                'email' => 'bail|required|string|email|max:100|unique:' . User::tableName() . ',email,' . $user->id,
                'phone' => 'bail|nullable|string|max:20|unique:' . User::tableName() . ',phone,' . $user->id,
                'password' => 'bail|nullable|min:6|confirmed',
            ];
        } else {
            $rules += [
                'email' => 'bail|required|string|email|max:100|unique:' . User::tableName() . ',email',
                'phone' => 'bail|nullable|string|max:20|unique:' . User::tableName() . ',phone',
                'password' => 'bail|required|min:6|confirmed',
            ];
        }

        return $rules;
    }

    /**
     * Get attribute name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => __('app/settings/user.request.name'),
            'lang' => __('app/settings/user.request.lang'),
            'role' => __('app/settings/user.request.role'),
            'avatar' => __('app/settings/user.request.avatar'),
            'password' => __('app/settings/user.request.password'),
            'email' => __('app/settings/user.request.email'),
            'phone' => __('app/settings/user.request.phone'),
        ];
    }
}
