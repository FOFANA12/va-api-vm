<?php

namespace App\Http\Requests;

use App\Models\ActionDomain;
use App\Models\FundingSource;
use App\Models\User;
use App\Support\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionDomainRequest extends FormRequest
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
            'description' => 'bail|nullable|string|max:1000',
            'prerequisites' => 'bail|nullable|string|max:1000',
            'impacts' => 'bail|nullable|string|max:1000',
            'risks' => 'bail|nullable|string|max:1000',

            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',

            'currency' => ['bail', 'required', Rule::in(Currency::codes())],
            'responsible'  => 'bail|nullable|uuid|exists:' . User::tableName() . ',uuid',

            'funding_sources' => 'bail|nullable|array',
            'funding_sources.*.uuid' => 'bail|required|exists:' . FundingSource::tableName() . ',uuid',
            'funding_sources.*.planned_amount' => 'nullable|numeric|min:0',
        ];

        if ($this->isMethod('put')) {
            $actionDomain = $this->route('action_domain');
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . ActionDomain::tableName() . ',name,' . $actionDomain->id,
            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . ActionDomain::tableName() . ',name'
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
            'name' => __('app/action_domain.request.name'),
            'start_date' => __('app/action_domain.request.start_date'),
            'end_date' => __('app/action_domain.request.end_date'),
            'currency' => __('app/action_domain.request.currency'),
            'responsible' => __('app/action_domain.request.responsible'),
            'description' => __('app/action_domain.request.description'),
            'prerequisites' => __('app/action_domain.request.prerequisites'),
            'impacts' => __('app/action_domain.request.impacts'),
            'risks' => __('app/action_domain.request.risks'),
            'funding_sources' => __('app/action_domain.request.funding_sources.title'),
        ];

        if (is_array($this->funding_sources)) {
            for ($i = 0; $i < count($this->funding_sources); ++$i) {
                $attributes += [
                    'funding_sources.' . $i . '.uuid' => __('app/action_domain.request.funding_sources.uuid') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                    'funding_sources.' . $i . '.planned_amount' => __('app/action_domain.request.funding_sources.planned_amount') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                ];
            }
        }

        return $attributes;
    }
}
