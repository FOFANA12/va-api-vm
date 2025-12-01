<?php

namespace App\Http\Requests;

use App\Models\ActionPlan;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionPlanRequest extends FormRequest
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
        $structure = $this->structure;

        $rules = [
            'description' => 'bail|nullable|string|max:1000',
            'start_date' => 'bail|nullable|date',
            'end_date' => 'bail|nullable|date|after_or_equal:start_date',
            'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
            'responsible' => 'bail|nullable|exists:' . User::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $actionPlan = $this->route('action_plan');
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'max:100',
                    Rule::unique(ActionPlan::tableName(), 'name')
                        ->ignore($actionPlan->id)
                        ->where(fn($query) => $query->where('structure_uuid', $structure)),
                ],
            ];
        } else {
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'max:100',
                    Rule::unique(ActionPlan::tableName(), 'name')
                        ->where(fn($query) => $query->where('structure_uuid', $structure)),
                ],
            ];
        }
        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (filter_var($this->status, FILTER_VALIDATE_BOOL)) {

                if ($this->isMethod('post')) {
                    $exist = ActionPlan::where('structure_uuid', $this->input('structure'))
                        ->where('status', true)
                        ->first();

                    if ($exist) {
                        $validator->errors()->add(
                            'structure',
                            __('app/action_plan.request.already_active_action_plan')
                        );
                    }
                }

                if ($this->isMethod('put')) {
                    $actionPlan = $this->route('action_plan');
                    $exist = ActionPlan::where('structure_uuid', $this->input('structure'))
                        ->where('status', true)
                        ->where('id', '<>', $actionPlan->id)
                        ->first();

                    if ($exist) {
                        $validator->errors()->add(
                            'structure',
                            __('app/action_plan.request.already_active_action_plan')
                        );
                    }
                }
            }
        });
    }

    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        return [
            'name' => __('app/action_plan.request.name'),
            'description' => __('app/action_plan.request.description'),
            'start_date' => __('app/action_plan.request.start_date'),
            'end_date' => __('app/action_plan.request.end_date'),
            'structure' => __('app/action_plan.request.structure'),
            'responsible' => __('app/action_plan.request.responsible'),
        ];
    }
}
