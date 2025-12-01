<?php

namespace App\Http\Requests\Settings;

use App\Models\Funder;
use Illuminate\Foundation\Http\FormRequest;

class FunderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'  => 'bail|required|string|max:100|unique:' . Funder::tableName() . ',name',
            'email' => 'bail|nullable|email|max:150|unique:' . Funder::tableName() . ',email',
            'phone' => 'bail|nullable|string|max:30|unique:' . Funder::tableName() . ',phone',
        ];

        if ($this->isMethod('put')) {
            $funder = $this->route('funder');

            $rules['name']  = 'bail|required|string|max:100|unique:' . Funder::tableName() . ',name,' . $funder->id;
            $rules['email'] = 'bail|nullable|email|max:150|unique:' . Funder::tableName() . ',email,' . $funder->id;
            $rules['phone'] = 'bail|nullable|string|max:30|unique:' . Funder::tableName() . ',phone,' . $funder->id;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name'   => __('app/settings/funder.request.name'),
            'email'  => __('app/settings/funder.request.email'),
            'phone'  => __('app/settings/funder.request.phone'),
        ];
    }
}
