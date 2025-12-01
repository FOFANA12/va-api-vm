<?php

namespace App\Http\Requests;

use App\Models\IndicatorCategory;
use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Support\ChartType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndicatorRequest extends FormRequest
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
            'category' => 'bail|required|exists:' . IndicatorCategory::tableName() . ',uuid',
            'name' => 'bail|required|string|max:100',
            'description' => 'bail|nullable|string|max:1000',
            'chart_type' => ['bail', 'required', Rule::in(ChartType::codes())],
            'initial_value' => 'bail|required|numeric|gte:0',
            'final_target_value' => 'bail|required|numeric|gt:0|gt:initial_value',
            'unit' => 'bail|required|string|max:20',
        ];

        if ($this->isMethod('post')) {
            $rules += [
                'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
                'strategic_map' => 'bail|required|exists:' . StrategicMap::tableName() . ',uuid',
                'strategic_objective' => 'bail|required|exists:' . StrategicObjective::tableName() . ',uuid',
            ];

            if ($this->structure) {
                $rules += [
                    'strategic_element' => 'bail|required|exists:' . StrategicElement::tableName() . ',uuid',
                ];
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'structure' => __('app/indicator.request.structure'),
            'strategic_map' => __('app/indicator.request.strategic_map'),
            'strategic_objective' => __('app/indicator.request.strategic_objective'),
            'category' => __('app/indicator.request.category'),

            'name' => __('app/indicator.request.name'),
            'description' => __('app/indicator.request.description'),
            'chart_type' => __('app/indicator.request.chart_type'),
            'initial_value' => __('app/indicator.request.initial_value'),
            'final_target_value' => __('app/indicator.request.final_target_value'),
            'unit' => __('app/indicator.request.unit'),
        ];

        $structureUuid = $this->input('structure');

        if ($structureUuid) {
            $structure = Structure::where('uuid', $structureUuid)->first();

            if ($structure && $structure->type === 'STATE') {
                $attributes['strategic_element'] = __('app/indicator.request.strategic_lever');
            } else {
                $attributes['strategic_element'] = __('app/indicator.request.strategic_axis');
            }
        }

        return $attributes;
    }
}
