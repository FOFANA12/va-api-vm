<?php

namespace App\Http\Requests\Settings;

use App\Models\FileType;
use Illuminate\Foundation\Http\FormRequest;

class FileTypeRequest extends FormRequest
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
            'name'  => 'bail|required|string|max:100|unique:' . FileType::tableName() . ',name',
            'file' => 'bail|nullable|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
        ];

        if ($this->isMethod('put')) {
            $fileType = $this->route('file_type');

            $rules['name']  = 'bail|required|string|max:100|unique:' . FileType::tableName() . ',name,' . $fileType->id;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/file_type.request.name'),
            'file' => __('app/settings/file_type.request.file'),
        ];
    }
}
