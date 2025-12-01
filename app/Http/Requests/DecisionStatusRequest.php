<?php

namespace App\Http\Requests;

use App\Models\Decision;
use App\Support\DecisionStatus;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DecisionStatusRequest extends FormRequest
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
            // 'status_date' => 'bail|required|date',
            'comment' => 'bail|required|string|max:1000',
            'status' => ['bail', 'required', Rule::in(DecisionStatus::codes())],
            'file' => 'bail|nullable|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
        ];
    }

    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        return [
            // 'status_date' => __('app/decision_status.request.status_date'),
            'comment' => __('app/decision_status.request.comment'),
            'status' => __('app/decision_status.request.status'),
            'file' => __('app/attachment.request.file'),
        ];
    }
}
