<?php

namespace App\Http\Requests;

use App\Models\Indicator;
use App\Support\FrequencyUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndicatorPlanningRequest extends FormRequest
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
            'frequency_unit' => ['bail', 'required', Rule::in(FrequencyUnit::codes())],
            'frequency_value' => 'bail|required|integer|min:1',

            'periods' => 'bail|required|array|min:1',
            'periods.*.start_date' => 'bail|required|date',
            'periods.*.end_date' => 'bail|required|date|after_or_equal:periods.*.start_date',
            'periods.*.target_value' => 'bail|required|numeric|gt:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $periods = $this->input('periods', []);

            if (!is_array($periods) || count($periods) === 0) {
                return;
            }
            $indicator = $this->route('indicator');

            $firstTarget = $periods[0]['target_value'] ?? null;
            if ($indicator && $firstTarget !== null && $firstTarget <= $indicator->initial_value) {
                $v->errors()->add(
                    "periods.0.target_value",
                    __('app/indicator_planning.plannings_error.first_target_gt_initial', [
                        'value' => $indicator->initial_value,
                    ]) . ' (' . __('app/common.request.line_number', ['line' => 1]) . ')'
                );
            }

            if ($indicator && $indicator->strategicObjective) {
                $objective = $indicator->strategicObjective;
                $objStart  = $objective->start_date->format('Y-m-d');
                $objEnd    = $objective->end_date->format('Y-m-d');

                foreach ($periods as $i => $period) {
                    $start = $period['start_date'] ?? null;
                    $end   = $period['end_date'] ?? null;

                    if ($start && ($start < $objStart || $start > $objEnd)) {
                        $v->errors()->add(
                            "periods.$i.start_date",
                            __('app/indicator_planning.plannings_error.start_date_outside', [
                                'min' => $objStart,
                                'max' => $objEnd,
                            ]) . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')'
                        );
                    }

                    if ($end && ($end < $objStart || $end > $objEnd)) {
                        $v->errors()->add(
                            "periods.$i.end_date",
                            __('app/indicator_planning.plannings_error.end_date_outside', [
                                'min' => $objStart,
                                'max' => $objEnd,
                            ]) . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')'
                        );
                    }
                }
            }

            $previous = null;

            foreach ($periods as $i => $period) {
                $current = (float) $period['target_value'];

                if ($previous !== null && $current <= $previous) {
                    $v->errors()->add(
                        "periods.$i.target_value",
                        __('app/indicator_planning.plannings_error.targets_increasing') . ' (' .
                            __('app/common.request.line_number', ['line' => $i + 1]) . ')'
                    );
                    break;
                }

                $previous = $current;
            }

            if ($indicator) {
                $lastIndex = count($periods) - 1;
                $lastValue = (float) end($periods)['target_value'];
                $expected = (float) $indicator->final_target_value;

                if ($lastValue < $expected) {
                    $v->errors()->add(
                        "periods.$lastIndex.target_value",
                        __('app/indicator_planning.plannings_error.last_target_must_be_greater_or_equal', [
                            'value' => $expected,
                        ]) . ' (' . __('app/common.request.line_number', ['line' => $lastIndex + 1]) . ')'
                    );
                }
            }

            for ($i = 1; $i < count($periods); $i++) {
                $prevEnd   = $periods[$i - 1]['end_date'] ?? null;
                $currStart = $periods[$i]['start_date'] ?? null;

                if ($prevEnd && $currStart && $currStart < $prevEnd) {
                    $v->errors()->add(
                        "periods.$i.start_date",
                        __('app/indicator_planning.plannings_error.periods_overlap', [
                            'line' => $i + 1,
                        ]) . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')'
                    );
                    break;
                }
            }
        });
    }



    public function messages()
    {
        return [
            'periods.required' => __('app/indicator_planning.plannings_error.required'),
        ];
    }

    public function attributes(): array
    {
        $attributes = [
            'frequency_unit' => __('app/indicator_planning.request.frequency_unit'),
            'frequency_value' => __('app/indicator_planning.request.frequency_value'),
            'periods' => __('app/indicator_planning.request.periods.title'),
        ];

        if (is_array($this->periods)) {
            for ($i = 0; $i < count($this->periods); ++$i) {
                $attributes += [
                    "periods.$i.start_date" => __('app/indicator_planning.request.periods.start_date') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                    "periods.$i.end_date" => __('app/indicator_planning.request.periods.end_date') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                    "periods.$i.target_value" => __('app/indicator_planning.request.periods.target_value') . ' (' . __('app/common.request.line_number', ['line' => $i + 1]) . ')',
                   
                ];
            }
        }

        return $attributes;
    }
}
