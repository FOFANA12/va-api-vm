<?php

namespace App\Http\Requests;

use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Support\PriorityLevel;
use App\Support\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StrategicObjectiveRequest extends FormRequest
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
        $strategicElement = $this->strategic_element;

        $rules = [
            'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
            'strategic_map' => 'bail|required|exists:' . StrategicMap::tableName() . ',uuid',
            'lead_structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
            'description' => 'bail|nullable|string|max:1000',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'priority' => ['bail', 'required', Rule::in(PriorityLevel::codes())],
            'risk_level' => ['bail', 'required', Rule::in(RiskLevel::codes())],
        ];

        if ($this->structure) {
            $rules += [
                'strategic_element' => 'bail|required|exists:' . StrategicElement::tableName() . ',uuid',
            ];
        }

        if ($this->isMethod('put')) {
            $strategicObjective = $this->route('strategic_objective');

            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:200',
                    Rule::unique(StrategicObjective::tableName(), 'name')
                        ->ignore($strategicObjective->id)
                        ->where(fn($query) => $query->where('strategic_element_uuid', $strategicElement)),
                ],
            ];
        } else {

            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:200',
                    Rule::unique(StrategicObjective::tableName(), 'name')
                        ->where(fn($query) => $query->where('strategic_element_uuid', $strategicElement)),
                ],
            ];
        }

        return $rules;
    }

    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        $attributes = [
            'structure' => __('app/strategic_objective.request.structure'),
            'strategic_map' => __('app/strategic_objective.request.strategic_map'),
            'lead_structure' => __('app/strategic_objective.request.lead_structure'),
            'name' => __('app/strategic_objective.request.name'),
            'description' => __('app/strategic_objective.request.description'),
            'start_date' => __('app/strategic_objective.request.start_date'),
            'end_date' => __('app/strategic_objective.request.end_date'),
            'priority' => __('app/strategic_objective.request.priority'),
            'risk_level' => __('app/strategic_objective.request.risk_level'),
        ];

        $structureUuid = $this->input('structure');

        if ($structureUuid) {
            $structure = Structure::where('uuid', $structureUuid)->first();

            if ($structure && $structure->type === 'STATE') {
                $attributes['strategic_element'] = __('app/strategic_objective.request.strategic_lever');
            } else {
                $attributes['strategic_element'] = __('app/strategic_objective.request.strategic_axis');
            }
        }

        return $attributes;
    }
}
