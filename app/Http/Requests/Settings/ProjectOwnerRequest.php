<?php

namespace App\Http\Requests\Settings;

use App\Models\ProjectOwner;
use App\Models\Structure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = ProjectOwner::tableName();
        $structure = $this->structure;

        $rules = [
            'structure' => 'bail|nullable|uuid|exists:' . Structure::tableName() . ',uuid',
            'type' => 'bail|nullable|string|max:50',
        ];

        if ($this->isMethod('put')) {
            $projectOwner = $this->route('projectOwner');

            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(ProjectOwner::tableName(), 'name')
                        ->ignore($projectOwner->id)
                        ->where(fn($query) => $query->where('structure_uuid', $structure)),
                ],
                'email' => 'bail|nullable|email|max:150|unique:' . $table . ',email,' . $projectOwner->id,
                'phone' => 'bail|nullable|string|max:30|unique:' . $table . ',phone,' . $projectOwner->id,
            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:100|unique:' . $table . ',name',
                'email' => 'bail|nullable|email|max:150|unique:' . $table . ',email',
                'phone' => 'bail|nullable|string|max:30|unique:' . $table . ',phone',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/project_owner.request.name'),
            'structure' => __('app/settings/project_owner.request.structure'),
            'type' => __('app/settings/project_owner.request.type'),
            'email' => __('app/settings/project_owner.request.email'),
            'phone' => __('app/settings/project_owner.request.phone'),
        ];
    }
}
