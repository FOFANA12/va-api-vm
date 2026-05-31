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
        $parentUuid = $this->input('parent');

        $rules = [
            'parent' => 'bail|nullable|exists:' . Structure::tableName() . ',uuid',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $structure = $this->route('structure');
            $rules += [
                'abbreviation' => [
                    'bail',
                    'required',
                    'string',
                    'max:20',
                    Rule::unique(Structure::tableName(), 'abbreviation')
                        ->where(fn($q) => $q->where('parent_uuid', $parentUuid))
                        ->ignore($structure->id)
                ],

                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(Structure::tableName(), 'name')
                        ->where(fn($q) => $q->where('parent_uuid', $parentUuid))
                        ->ignore($structure->id)
                ],
            ];
        } else {
            $rules += [

                'type' => ['bail', 'required', Rule::in(StructureType::codes())],

                'abbreviation' => [
                    'bail',
                    'required',
                    'string',
                    'max:20',
                    Rule::unique(Structure::tableName(), 'abbreviation')
                        ->where(fn($q) => $q->where('parent_uuid', $parentUuid))
                ],

                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(Structure::tableName(), 'name')
                        ->where(fn($q) => $q->where('parent_uuid', $parentUuid))
                ],
            ];
        }

        return $rules;
    }

    /**
     * Custom validator logic to validate parent-child type relationships.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $parentUuid = $this->input('parent');
            $structure = ($this->isMethod('put') || $this->isMethod('patch'))
                ? $this->route('structure')
                : null;
            $type = strtoupper($this->input('type', $structure?->type ?? ''));

            if ($type === 'STATE' && !empty($parentUuid)) {
                $validator->errors()->add('parent', __('app/structure.validation.state_no_parent'));
                return;
            }

            if (empty($parentUuid)) {
                return;
            }

            $visited = [];
            $parent = Structure::where('uuid', $parentUuid)->first();

            while ($parent) {
                if ($structure && $parent->uuid === $structure->uuid) {
                    $validator->errors()->add('parent', __('app/structure.validation.parent_cycle'));
                    return;
                }

                if (isset($visited[$parent->uuid])) {
                    $validator->errors()->add('parent', __('app/structure.validation.parent_cycle'));
                    return;
                }

                $visited[$parent->uuid] = true;

                if (empty($parent->parent_uuid)) {
                    return;
                }

                $parent = Structure::where('uuid', $parent->parent_uuid)->first();
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
