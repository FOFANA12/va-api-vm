<?php

namespace App\Http\Requests;

use App\Models\Structure;
use Illuminate\Foundation\Http\FormRequest;

class ActionPlanImportRequest extends FormRequest
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
            'file' => 'bail|required|file|mimes:xlsx,xls,csv|max:5120',
            'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
        ];
    }

    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        return [
            'file' => __('app/attachment.request.file'),
            'structure' => __('app/action_plan.request.structure'),
        ];
    }
}
