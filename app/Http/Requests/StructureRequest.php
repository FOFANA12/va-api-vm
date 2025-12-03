<?php

namespace App\Http\Requests;

use App\Models\Structure;
use App\Support\StructureType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StructureRequest extends FormRequest
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
            'parent' => 'bail|nullable|exists:' . Structure::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $structure = $this->route('structure');
            $rules += [
                'abbreviation' => 'bail|required|string|max:20|unique:' . Structure::tableName() . ',abbreviation,' . $structure->id,
                'name' => 'bail|required|string|max:100|unique:' . Structure::tableName() . ',name,' . $structure->id
            ];
        } else {
            $rules += [
                'type' => ['bail', 'required', Rule::in(StructureType::codes())],
                'abbreviation' => 'bail|required|string|max:20|unique:' . Structure::tableName() . ',abbreviation',
                'name' => 'bail|required|string|max:100|unique:' . Structure::tableName() . ',name',
            ];
        }

        return $rules;
    }

    /**
     * Custom validator logic to validate parent-child type relationships.
     */
    public function withValidator($validator): void
    {
        if (!$this->isMethod('post')) {
            return;
        }

        $validator->after(function ($validator) {
            $type = strtoupper($this->input('type'));
            $parentUuid = $this->input('parent');

            if ($type === 'STATE' && !empty($parentUuid)) {
                $validator->errors()->add('parent', __('app/structure.validation.state_no_parent'));
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
            'name' => __('app/structure.request.name'),
            'abbreviation' => __('app/structure.request.abbreviation'),
            'parent' => __('app/structure.request.parent'),
            'type' => __('app/structure.request.type'),
        ];
    }
}
