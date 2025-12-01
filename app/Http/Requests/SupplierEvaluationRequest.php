<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'score_delay'   => 'bail|required|integer|min:0|max:5',
            'score_price'   => 'bail|required|integer|min:0|max:5',
            'score_quality' => 'bail|required|integer|min:0|max:5',
            'comment'       => 'bail|nullable|string|max:1000',
        ];
    }

    public function attributes(): array
    {
        return [
            'score_delay'   => __('app/supplier_evaluation.request.score_delay'),
            'score_price'   => __('app/supplier_evaluation.request.score_price'),
            'score_quality' => __('app/supplier_evaluation.request.score_quality'),
            'comment'       => __('app/supplier_evaluation.request.comment'),
        ];
    }
}
