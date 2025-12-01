<?php

namespace App\Http\Requests\Settings;

use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
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
            'name' => 'bail|required|string|max:20',
        ];

        if($this->isMethod('put')){
            $currency = $this->route('currency');
            $rules += [
                'code' => 'bail|required|max:5|unique:'.Currency::tableName(). ',code,'.$currency->id,
            ];
        }else{
            $rules +=[
                'code' => 'bail|required|max:5|unique:'.Currency::tableName(). ',code'
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
            'name' => __('app/settings/currency.request.name'),
            'code' => __('app/settings/currency.request.code'),
        ];
    }
}
