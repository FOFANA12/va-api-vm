<?php

namespace App\Http\Requests;

use App\Support\Currency;
use App\Models\Program;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'description' => 'bail|nullable|string|max:1000',
            'prerequisites' => 'bail|nullable|string|max:1000',
            'impacts' => 'bail|nullable|string|max:1000',
            'risks' => 'bail|nullable|string|max:1000',

            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'currency' => ['bail', 'required', Rule::in(Currency::codes())],
            'program' => 'bail|nullable|exists:' . Program::tableName() . ',uuid',
            'responsible' => 'bail|nullable|exists:' . User::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $project = $this->route('project');
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . Project::tableName() . ',name,' . $project->id,
            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . Project::tableName() . ',name'
            ];
        }
        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'program' => __('app/project.request.program'),
            'name' => __('app/project.request.name'),
            'start_date' => __('app/project.request.start_date'),
            'end_date' => __('app/project.request.end_date'),
            'currency' => __('app/project.request.currency'),
            'responsible' => __('app/project.request.responsible'),

            'description' => __('app/project.request.description'),
            'prerequisites' => __('app/project.request.prerequisites'),
            'impacts' => __('app/project.request.impacts'),
            'risks' => __('app/project.request.risks'),
            'funding_sources' => __('app/project.request.funding_sources.title'),
        ];

        if (is_array($this->funding_sources)) {
            for ($i = 0; $i < count($this->funding_sources); ++$i) {
                $attributes += [
                    'funding_sources.' . $i . '.uuid' => __('app/project.request.funding_sources.uuid') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                    'funding_sources.' . $i . '.planned_amount' => __('app/project.request.funding_sources.planned_amount') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                ];
            }
        }

        return $attributes;
    }
}
