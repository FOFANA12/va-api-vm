<?php

namespace App\Http\Requests;

use App\Support\FrequencyUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionPlanningRequest extends FormRequest
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
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'frequency_unit' => ['bail', 'required', Rule::in(FrequencyUnit::codes())],
            'frequency_value' => 'bail|required|integer|min:1',

            'periods' => 'bail|required|array|min:1',
            'periods.*.start_date' => 'bail|required|date',
            'periods.*.end_date' => 'bail|required|date|after_or_equal:periods.*.start_date',
            'periods.*.progress_percent' => 'bail|nullable|numeric|min:0|max:100',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $periods = $this->input('periods', []);

            if (!is_array($periods) || count($periods) === 0) {
                return;
            }

            $values = [];
            foreach ($periods as $i => $p) {
                $v = $p['progress_percent'] ?? null;

                if ($v === null || $v === '') {
                    $validator->errors()->add("periods.$i.progress_percent", __('app/action_planning.plannings_error.progress_required'));
                    continue;
                }

                $v = floatval($v);
                if ($v < 0 || $v > 100) {
                    $validator->errors()->add("periods.$i.progress_percent", __('validation.between.numeric', [
                        'attribute' => __('app/action_planning.request.periods.progress_percent'),
                        'min' => 0,
                        'max' => 100,
                    ]));
                    continue;
                }

                $values[$i] = $v;
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            for ($i = 0; $i < count($values) - 1; $i++) {
                if ($values[$i + 1] <= $values[$i]) {
                    $validator->errors()->add('periods', __('app/action_planning.plannings_error.not_strictly_increasing'));
                    break;
                }
            }

            $last = end($values);
            if ((int) round($last) !== 100) {
                $validator->errors()->add('periods', __('app/action_planning.plannings_error.last_not_100'));
            }
        });
    }


    public function messages()
    {
        return [
            'periods.required' => __('app/action_planning.plannings_error.required'),
        ];
    }

    public function attributes(): array
    {
        $attributes = [
            'start_date' => __('app/action_planning.request.start_date'),
            'end_date' => __('app/action_planning.request.end_date'),
            'frequency_unit' => __('app/action_planning.request.frequency_unit'),
            'frequency_value' => __('app/action_planning.request.frequency_value'),
            'periods' => __('app/action_planning.request.periods.title'),
        ];

        if (is_array($this->periods)) {
            for ($i = 0; $i < count($this->periods); ++$i) {
                $attributes += [
                    "periods.$i.start_date" => __('app/action_planning.request.periods.start_date') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                    "periods.$i.end_date" => __('app/action_planning.request.periods.end_date') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                    "periods.$i.progress_percent" => __('app/action_planning.request.periods.progress_percent') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                ];
            }
        }

        return $attributes;
    }
}
