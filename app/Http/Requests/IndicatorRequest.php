<?php

namespace App\Http\Requests;

use App\Models\IndicatorCategory;
use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Support\ChartType;
use App\Support\IndicatorStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
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
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $objectiveUuid = $this->input('strategic_objective')
                ?? $this->route('indicator')?->strategic_objective_uuid;

            if (
                !$objectiveUuid
                || !$this->start_date
                || !$this->end_date
                || $validator->errors()->has('start_date')
                || $validator->errors()->has('end_date')
            ) {
                return;
            }

            $objective = StrategicObjective::where('uuid', $objectiveUuid)->first();

            if (!$objective) {
                return;
            }

            if (
                $this->isMethod('post')
                && !IndicatorStatus::isAllowedForObjectiveStatus(
                    IndicatorStatus::CREATED,
                    $objective->status
                )
            ) {
                $validator->errors()->add(
                    'strategic_objective',
                    __('app/indicator.document_not_editable_status')
                );
            }

            $indicatorStart = Carbon::parse($this->start_date)->toDateString();
            $indicatorEnd = Carbon::parse($this->end_date)->toDateString();
            $objectiveStart = $objective->start_date->toDateString();
            $objectiveEnd = $objective->end_date->toDateString();

            if ($indicatorStart < $objectiveStart || $indicatorStart > $objectiveEnd) {
                $validator->errors()->add(
                    'start_date',
                    __('app/indicator.request.start_date_outside_objective')
                );
            }

            if ($indicatorEnd < $objectiveStart || $indicatorEnd > $objectiveEnd) {
                $validator->errors()->add(
                    'end_date',
                    __('app/indicator.request.end_date_outside_objective')
                );
            }
        });
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
            'start_date' => __('app/indicator.request.start_date'),
            'end_date' => __('app/indicator.request.end_date'),
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
