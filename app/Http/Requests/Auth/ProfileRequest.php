<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
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
        $user = Auth::user();

        $rules = [
            'name' => 'bail|required|max:100',
            'email' => 'bail|required|email|max:100|unique:' . User::tableName() . ',email,' . $user->id,
            'phone' => 'bail|nullable|string|max:20|unique:' . User::tableName() . ',phone,' . $user->id,
            'lang' => ['bail', 'required', Rule::in(Language::codes())],
            'avatar' => 'bail|nullable|max:5120|mimes:jpeg,png,jpg',
            'password' => 'bail|nullable|min:6|confirmed',
        ];

        if ($user->employee) {
            $rules += [
                'job_title' => 'bail|nullable|string|max:50',
                'floor' => 'bail|nullable|string|max:10',
                'office' => 'bail|nullable|string|max:20',
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
            'name' => __('app/auth/profile.request.name'),
            'email' => __('app/auth/profile.request.email'),
            'phone' => __('app/auth/profile.request.phone'),
            'lang' => __('app/auth/profile.request.lang'),
            'avatar' => __('app/auth/profile.request.avatar'),
            'password' => __('app/auth/profile.request.password'),
            'job_title' => __('app/auth/profile.request.job_title'),
            'floor' => __('app/auth/profile.request.floor'),
            'office' => __('app/auth/profile.request.office'),
        ];
    }
}
