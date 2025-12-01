<?php

namespace App\Http\Requests;

use App\Models\IndicatorPeriod;
use Illuminate\Foundation\Http\FormRequest;

class IndicatorControlRequest extends FormRequest
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
            'root_cause' => 'bail|nullable|string|max:1000',
            'indicator_period' => 'bail|required|exists:' . IndicatorPeriod::tableName() . ',uuid',
            'control_date' => 'bail|required|date',
            'achieved_value' => 'bail|required|numeric|gte:0',
            'file' => 'bail|nullable|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
        ];
    }


    public function attributes(): array
    {
        return [
            'root_cause' => __('app/indicator_control.request.root_cause'),
            'inicator_period' => __('app/indicator_control.request.inicator_period'),
            'control_date' => __('app/indicator_control.request.control_date'),
            'achieved_value' => __('app/indicator_control.request.achieved_value'),
            'file' => __('app/attachment.request.file'),
        ];;
    }
}
