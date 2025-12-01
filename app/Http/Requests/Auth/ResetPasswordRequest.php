<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6|confirmed',
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
                $validator->errors()->add('email', __('app/auth/common.email_not_found'));
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

        });
    }
    

    /**
     * Get attribute names for translations.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'email' => __('app/auth/reset_password.request.email'),
            'password' => __('app/auth/reset_password.request.password'),
        ];
    }
}
