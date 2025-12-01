<?php

namespace App\Http\Requests\Settings;

use App\Models\PaymentMode;
use Illuminate\Foundation\Http\FormRequest;

class PaymentModeRequest extends FormRequest
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
            'name'  => 'bail|required|string|max:50|unique:' . PaymentMode::tableName() . ',name',
        ];

        if ($this->isMethod('put')) {
            $paymentMode = $this->route('payment_mode'); 

            $rules['name']  = 'bail|required|string|max:50|unique:' . PaymentMode::tableName() . ',name,' . $paymentMode->id;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/payment_mode.request.name'),
        ];
    }
}
