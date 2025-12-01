<?php

namespace App\Http\Requests;

use App\Models\ActionControl;
use App\Models\ActionPeriod;
use App\Models\ActionPhase;
use Illuminate\Foundation\Http\FormRequest;

class ActionControlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        if ($this->items) {
            $this->merge([
                'items' => json_decode($this->items, true),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'root_cause' => 'bail|nullable|string|max:1000',
            'control_date' => 'bail|required|date',
            'file' => 'bail|nullable|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',

            'items' => 'bail|required|array|min:1',
            'items.*.phase' => 'bail|required|exists:' . ActionPhase::tableName() . ',uuid',
            'items.*.progress_percent' => 'bail|required|numeric|min:0|max:100',
        ];
    }


    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('POST')) {
                $actionPeriodUuid = $this->route('action_period')?->uuid;

                $actionPeriod = ActionPeriod::with('action')->where('uuid', $actionPeriodUuid)->first();

                if (!$actionPeriod) {
                    $validator->errors()->add(
                        'action_period',
                        __('app/action_control.controls_error.period_not_found')
                    );
                }

                if ($actionPeriod) {
                    $action = $actionPeriod->action;

                    if (!$action || $action->status !== 'in_progress') {
                        $validator->errors()->add(
                            'action_period',
                            __('app/action_control.controls_error.action_not_in_progress')
                        );
                    }

                    $existingControl = ActionControl::where('action_period_uuid', $this->action_period)->first();
                    if ($existingControl) {
                        $validator->errors()->add(
                            'action_period',
                            __('app/action_control.controls_error.period_already_controlled')
                        );
                    }
                }
            }
        });
    }


    public function messages()
    {
        return [
            'items.required' => __('app/action_control.controls_error.required'),
        ];
    }

    public function attributes(): array
    {
        $attributes = [
            'root_cause' => __('app/action_control.request.root_cause'),
            'control_date' => __('app/action_control.request.control_date'),
            'items' => __('app/action_control.request.items.title'),
            'file' => __('app/attachment.request.file'),
        ];

        if (is_array($this->items)) {
            for ($i = 0; $i < count($this->items); ++$i) {
                $attributes += [
                    "items.$i.phase" => __('app/action_control.request.items.phase') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                    "items.$i.progress_percent" => __('app/action_control.request.items.progress_percent') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                ];
            }
        }

        return $attributes;
    }
}
