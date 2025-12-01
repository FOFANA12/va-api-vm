<?php

namespace App\Http\Requests;

use App\Models\StrategicObjective;
use Illuminate\Foundation\Http\FormRequest;

class ActionAlignmentRequest extends FormRequest
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
            'lines' => 'bail|required|array|min:1',
            'lines.*.objective' => 'bail|required|exists:' . StrategicObjective::tableName() . ',uuid',
        ];
    }

    public function messages()
    {
        return [
            'lines.required' => __('app/alignment.request.lines.objective_required'),
        ];
    }

    public function attributes(): array
    {
        $attributes = [];

        if (is_array($this->lines)) {
            for ($i = 0; $i < count($this->lines); $i++) {
                $attributes["lines.$i.objective"] = __('app/alignment.request.lines.objective') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')';
            }
        }

        return $attributes;
    }
}
