<?php

namespace App\Http\Requests;

use App\Models\Activity;
use App\Support\Currency;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
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

            'project' => 'bail|nullable|exists:' . Project::tableName() . ',uuid',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'currency' => ['bail', 'required', Rule::in(Currency::codes())],
            'responsible'       => 'bail|nullable|exists:' . User::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $activity = $this->route('activity');
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . Activity::tableName() . ',name,' . $activity->id,
            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . Activity::tableName() . ',name'
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'project' => __('app/activity.request.project'),
            'name' => __('app/activity.request.name'),
            'start_date' => __('app/activity.request.start_date'),
            'end_date' => __('app/activity.request.end_date'),
            'budget' => __('app/activity.request.budget'),
            'currency' => __('app/activity.request.currency'),
            'responsible'       => __('app/activity.request.responsible'),

            'description' => __('app/activity.request.description'),
            'prerequisites' => __('app/activity.request.prerequisites'),
            'impacts' => __('app/activity.request.impacts'),
            'risks' => __('app/activity.request.risks'),
            'funding_sources' => __('app/activity.request.funding_sources.title'),
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
