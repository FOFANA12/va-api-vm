<?php

namespace App\Http\Requests;

use App\Models\CapabilityDomain;
use App\Support\Currency;
use App\Models\User;
use App\Models\StrategicDomain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CapabilityDomainRequest extends FormRequest
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

            'strategic_domain' => 'bail|nullable|exists:' . StrategicDomain::tableName() . ',uuid',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'currency' => ['bail', 'required', Rule::in(Currency::codes())],
            'responsible'       => 'bail|nullable|exists:' . User::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $capabilityDomain = $this->route('capability_domain');
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . CapabilityDomain::tableName() . ',name,' . $capabilityDomain->id,
            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . CapabilityDomain::tableName() . ',name'
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'strategic_domain' => __('app/capability_domain.request.strategic_domain'),
            'name' => __('app/capability_domain.request.name'),
            'start_date' => __('app/capability_domain.request.start_date'),
            'end_date' => __('app/capability_domain.request.end_date'),
            'budget' => __('app/capability_domain.request.budget'),
            'currency' => __('app/capability_domain.request.currency'),
            'responsible'       => __('app/capability_domain.request.responsible'),

            'description' => __('app/capability_domain.request.description'),
            'prerequisites' => __('app/capability_domain.request.prerequisites'),
            'impacts' => __('app/capability_domain.request.impacts'),
            'risks' => __('app/capability_domain.request.risks'),
            'funding_sources' => __('app/capability_domain.request.funding_sources.title'),
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
