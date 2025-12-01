<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
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
            'email' => 'bail|required|email',
            'password' => 'bail|required',
        ];
    }

    /**
     * Apply custom validation after the rules are checked.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function ($validator) {

            $user = User::where('email', $this->input('email'))->with('employee')->first();

            if (!$user) {
                $validator->errors()->add('email', __('app/auth/common.failed'));
                return;
            }

            if (!$user->status) {
                $validator->errors()->add('email', __('app/auth/common.inactive'));
                return;
            }

            if ($user->employee && !$user->employee->can_logged_in) {
                $validator->errors()->add('email', __('app/auth/common.cannot_login'));
                return;
            }

            if (!Hash::check($this->input('password'), $user->password)) {
                $validator->errors()->add('email', __('app/auth/common.failed'));
                return;
            }
        });
    }

    /**
     * Get attribute name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => __('app/auth/login.request.email'),
            'password' => __('app/auth/login.request.password'),
        ];
    }
}
