<?php

namespace App\Http\Requests;

use App\Models\FileType;
use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
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
            'title' => 'required|string|max:100',
            'file' => 'bail|required|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
            'attachable_type' => 'bail|required|string|max:100',
            'attachable_id' => 'bail|required',
            'comment' => 'bail|nullable|string|max:1000',
        ];

        if ($this->attachable_type === 'actions') {
            $rules += [
                'file_type' => 'bail|required|exists:' . FileType::tableName() . ',uuid',
            ];
        }

        return $rules;
    }

    /**
     * Get custom attribute names for translations.
     */
    public function attributes(): array
    {
        return [
            'title' => __('app/attachment.request.title'),
            'file' => __('app/attachment.request.file'),
            'attachable_type' => __('app/attachment.request.attachable_type'),
            'attachable_id' => __('app/attachment.request.attachable_id'),
            'file_type' => __('app/attachment.request.file_type'),
            'comment' => __('app/attachment.request.comment'),
        ];
    }
}
