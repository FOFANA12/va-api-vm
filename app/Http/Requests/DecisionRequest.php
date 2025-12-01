<?php

namespace App\Http\Requests;

use App\Support\PriorityLevel;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DecisionRequest extends FormRequest
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
            'decision_date' => 'bail|required|date',
            'title' => 'bail|required|string|max:150',
            'description' => 'bail|required|string|max:1000',
            'priority' => ['bail', 'required', Rule::in(PriorityLevel::codes())],
            'file' => 'bail|nullable|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
        ];
    }

    public function withValidator($validator): void
    {
        if (! $this->isMethod('post')) {
            return;
        }

        $validator->after(function ($v) {
            $type = strtolower(trim((string) $this->input('decidable_type')));
            $id = $this->input('decidable_id');

            if (blank($type) || blank($id)) {
                $v->errors()->add(
                    'decidable',
                    __('app/decision.errors.morph_invalid')
                );
                return;
            }

            $allowedTypes = ['actions', 'strategic_objectives', 'indicators'];

            if (! in_array($type, $allowedTypes, true)) {
                $v->errors()->add(
                    'decidable',
                    __('app/decision.errors.morph_invalid')
                );

                return;
            }
        });
    }

    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        return [
            'decision_date' => __('app/decision.request.decision_date'),
            'title' => __('app/decision.request.title'),
            'description' => __('app/decision.request.description'),
            'priority' => __('app/decision.request.priority'),
            'file' => __('app/attachment.request.file'),
        ];
    }
}
