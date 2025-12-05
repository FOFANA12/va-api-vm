<?php

namespace App\Http\Requests;

use App\Models\CapabilityDomain;
use App\Models\ElementaryLevel;
use App\Support\Currency;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ElementaryLevelRequest extends FormRequest
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

            'capability_domain' => 'bail|nullable|exists:' . CapabilityDomain::tableName() . ',uuid',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'currency' => ['bail', 'required', Rule::in(Currency::codes())],
            'responsible'       => 'bail|nullable|exists:' . User::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $elementaryLevel = $this->route('elementary_level');
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . ElementaryLevel::tableName() . ',name,' . $elementaryLevel->id,
            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . ElementaryLevel::tableName() . ',name'
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'capability_domain' => __('app/elementary_level.request.capability_domain'),
            'name' => __('app/elementary_level.request.name'),
            'start_date' => __('app/elementary_level.request.start_date'),
            'end_date' => __('app/elementary_level.request.end_date'),
            'budget' => __('app/elementary_level.request.budget'),
            'currency' => __('app/elementary_level.request.currency'),
            'responsible'       => __('app/elementary_level.request.responsible'),

            'description' => __('app/elementary_level.request.description'),
            'prerequisites' => __('app/elementary_level.request.prerequisites'),
            'impacts' => __('app/elementary_level.request.impacts'),
            'risks' => __('app/elementary_level.request.risks'),
            'funding_sources' => __('app/elementary_level.request.funding_sources.title'),
        ];

        if (is_array($this->funding_sources)) {
            for ($i = 0; $i < count($this->funding_sources); ++$i) {
                $attributes += [
                    'funding_sources.' . $i . '.uuid' => __('app/strategic_domain.request.funding_sources.uuid') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                    'funding_sources.' . $i . '.planned_amount' => __('app/strategic_domain.request.funding_sources.planned_amount') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                ];
            }
        }

        return $attributes;
    }
}
