<?php

namespace App\Http\Requests\Settings;

use App\Models\DefaultPhase;
use Illuminate\Foundation\Http\FormRequest;

class DefaultPhaseRequest extends FormRequest
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
            'duration' => 'bail|required|integer|gt:0',
            'weight' => 'bail|required|numeric|min:0|max:1',
            'number' => 'bail|required|integer|min:0|max:200',
        ];

        if ($this->isMethod('put')) {
            $defaultPhase = $this->route('default_phase');

            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . DefaultPhase::tableName() . ',name,' . $defaultPhase->id,

            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . DefaultPhase::tableName() . ',name',
            ];
        }

        return $rules;
    }

    /**
     * Custom validation logic.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $phase = $this->route('default_phase');
            $newWeight = (float) ($this->input('weight') ?? 0);

            $query = DefaultPhase::query();

            if ($phase?->id) {
                $query->where('id', '!=', $phase->id);
            }

            $sumOthers = (float) $query->sum('weight');
            $total = $sumOthers + $newWeight;

            $epsilon = 1e-8;

            if ($total > 1 + $epsilon) {
                $remaining = max(0, 1 - $sumOthers);
                $validator->errors()->add(
                    'weight',
                    __('app/settings/default_phase.errors.weight_overflow', [
                        'remaining' => number_format($remaining, 2, '.', ''),
                    ])
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/default_phase.request.name'),
            'duration' => __('app/settings/default_phase.request.duration'),
            'weight' => __('app/settings/default_phase.request.weight'),
            'number' => __('app/settings/default_phase.request.number'),
        ];
    }
}
