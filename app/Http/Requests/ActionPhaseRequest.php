<?php

namespace App\Http\Requests;

use App\Models\Action;
use App\Models\ActionPhase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionPhaseRequest extends FormRequest
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
        $actionId = $this->route('action');
        $action = !$actionId instanceof Action ? Action::findOrFail($actionId) : $actionId;
        $actionPhase = $this->route('action_phase');

        $rules = [
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'weight' => 'bail|required|numeric|min:0|max:1',
            'number' => 'bail|required|integer|min:0|max:200',
            'description' => 'bail|nullable|string|max:1000',
            'deliverable' => 'bail|nullable|string|max:1000',
        ];

        if ($this->isMethod('put')) {
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(ActionPhase::tableName(), 'name')
                        ->ignore($actionPhase->id)
                        ->where(fn($query) => $query->where('action_uuid', $action->uuid)),
                ],
            ];
        } else {
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(ActionPhase::tableName(), 'name')
                        ->where(fn($query) => $query->where('action_uuid', $action->uuid)),
                ],
            ];
        }


        return $rules;
    }

    public function withValidator($validator): void
    {
        $actionId = $this->route('action');
        $action = !$actionId instanceof Action ? Action::findOrFail($actionId) : $actionId;
        $actionPhase = $this->route('action_phase');

        $validator->after(function ($validator) use ($action, $actionPhase) {
            if (!$action) {
                return;
            }

            if (empty($action->start_date) || empty($action->end_date)) {
                $validator->errors()->add(
                    'action',
                    __('app/action_phase.errors.action_not_planned')
                );
                return;
            }

            $phaseStart = $this->input('start_date');
            $phaseEnd = $this->input('end_date');

            if ($phaseStart < $action->start_date) {
                $validator->errors()->add(
                    'start_date',
                    __('app/action_phase.errors.out_of_bounds_start')
                );
            }
            if ($phaseEnd > $action->end_date) {
                $validator->errors()->add(
                    'end_date',
                    __('app/action_phase.errors.out_of_bounds_end')
                );
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $newWeight = (float) ($this->input('weight') ?? 0);

            $query = ActionPhase::query()
                ->where('action_uuid', $action->uuid);

            if ($actionPhase?->id) {
                $query->where('id', '!=', $actionPhase->id);
            }

            $sumOthers = (float) $query->sum('weight');
            $total = $sumOthers + $newWeight;

            $epsilon = 1e-8;
            if ($total > 1 + $epsilon) {
                $remaining = max(0, 1 - $sumOthers);
                $validator->errors()->add(
                    'weight',
                    __('app/action_phase.errors.weight_overflow', [
                        'remaining' => number_format($remaining, 2, '.', ''),
                    ])
                );
            }
        });
    }


    public function attributes(): array
    {
        return [
            'name' => __('app/action_phase.request.name'),
            'start_date' => __('app/action_phase.request.start_date'),
            'end_date' => __('app/action_phase.request.end_date'),
            'weight' => __('app/action_phase.request.weight'),
            'number' => __('app/action_phase.request.number'),
            'description' => __('app/action_phase.request.description'),
            'deliverable' => __('app/action_phase.request.deliverable'),
        ];
    }
}
