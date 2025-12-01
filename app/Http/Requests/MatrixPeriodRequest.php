<?php

namespace App\Http\Requests;

use App\Helpers\DateTimeFormatter;
use Illuminate\Foundation\Http\FormRequest;

class MatrixPeriodRequest extends FormRequest
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
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $periodStart = $this->input('start_date');
            $periodEnd = $this->input('end_date');
            $strategicMap = null;

            if ($this->isMethod('post')) {
                $strategicMap = $this->route('strategic_map');
            } elseif ($this->isMethod('put')) {
                $matrixPeriod = $this->route('matrix_period');
                $strategicMap = $matrixPeriod->strategicMap;
            }

            // strategicMap
            if ($strategicMap) {
                $mapStart = $strategicMap->start_date;
                $mapEnd = $strategicMap->end_date;

                if ($periodStart < $mapStart || $periodEnd > $mapEnd) {
                    $validator->errors()->add(
                        'invalid_date_range',
                        __('app/matrix_period.request.invalid_date_range', [
                            'start' => DateTimeFormatter::formatDate($mapStart),
                            'end' => DateTimeFormatter::formatDate($mapEnd),
                        ])
                    );
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
            'start_date' => __('app/matrix_period.request.start_date'),
            'end_date' => __('app/matrix_period.request.end_date'),
        ];
    }
}
