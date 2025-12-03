<?php

namespace App\Http\Requests;

use App\Models\ActionDomain;
use App\Models\ActionPlan;
use App\Models\CapabilityDomain;
use App\Models\DelegatedProjectOwner;
use App\Models\Department;
use App\Models\FundingSource;
use App\Models\Municipality;
use App\Models\ProjectOwner;
use App\Models\Region;
use App\Models\StrategicDomain;
use App\Models\Structure;
use App\Support\Currency;
use App\Support\GenerateDocumentTypes;
use App\Support\PriorityLevel;
use App\Support\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionRequest extends FormRequest
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
            'name' => 'bail|required|string|max:100',
            'priority' => ['bail', 'required', Rule::in(PriorityLevel::codes())],
            'risk_level' => ['bail', 'required', Rule::in(RiskLevel::codes())],
            'generate_document_type' => ['bail', 'required', Rule::in(GenerateDocumentTypes::codes())],
            'currency' => ['bail', 'required', Rule::in(Currency::codes())],

            'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
            'action_plan' => 'bail|required|exists:' . ActionPlan::tableName() . ',uuid',
            'project_owner' => 'bail|required|exists:' . ProjectOwner::tableName() . ',uuid',
            'delegated_project_owner' => 'bail|required|exists:' . DelegatedProjectOwner::tableName() . ',uuid',

            'actionDomain' => 'bail|nullable|exists:' . ActionDomain::tableName() . ',uuid',
            'strategic_domain' => 'bail|nullable|exists:' . StrategicDomain::tableName() . ',uuid',
            'capability_domain' => 'bail|nullable|exists:' . CapabilityDomain::tableName() . ',uuid',
            'region' => 'bail|nullable|exists:' . Region::tableName() . ',uuid',
            'department' => 'bail|nullable|exists:' . Department::tableName() . ',uuid',
            'municipality' => 'bail|nullable|exists:' . Municipality::tableName() . ',uuid',

            'description' => 'bail|nullable|string|max:1000',
            'prerequisites' => 'bail|nullable|string|max:1000',
            'impacts' => 'bail|nullable|string|max:1000',
            'risks' => 'bail|nullable|string|max:1000',

            'funding_sources' => 'bail|nullable|array',
            'funding_sources.*.uuid' => 'bail|required|exists:' . FundingSource::tableName() . ',uuid',
            'funding_sources.*.planned_amount' => 'nullable|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        $attributes = [
            'name' => __('app/action.request.name'),
            'priority' => __('app/action.request.priority'),
            'risk_level' => __('app/action.request.risk_level'),
            'generate_document_type' => __('app/action.request.generate_document_type'),
            'currency' => __('app/action.request.currency'),

            'structure' => __('app/action.request.structure'),
            'action_plan' => __('app/action.request.action_plan'),
            'project_owner' => __('app/action.request.project_owner'),
            'delegated_project_owner' => __('app/action.request.delegated_project_owner'),

            'action_domain' => __('app/action.request.action_domain'),
            'strategic_domain' => __('app/action.request.strategic_domain'),
            'capability_domain' => __('app/action.request.capability_domain'),
            'region' => __('app/action.request.region'),
            'department' => __('app/action.request.department'),
            'municipality' => __('app/action.request.municipality'),

            'description' => __('app/action.request.description'),
            'prerequisites' => __('app/action.request.prerequisites'),
            'impacts' => __('app/action.request.impacts'),
            'risks' => __('app/action.request.risks'),

            'funding_sources' => __('app/action.request.funding_sources.title'),
        ];

        if (is_array($this->funding_sources)) {
            for ($i = 0; $i < count($this->funding_sources); ++$i) {
                $attributes += [
                    'funding_sources.' . $i . '.uuid' => __('app/action.request.funding_sources.uuid') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                    'funding_sources.' . $i . '.planned_amount' => __('app/action.request.funding_sources.planned_amount') . ' (' . __('app/common.request.line_number', ['line' => ($i + 1)]) . ')',
                ];
            }
        }

        return $attributes;
    }
}
