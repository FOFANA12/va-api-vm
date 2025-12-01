<?php

namespace App\Http\Requests\Settings;

use App\Models\DelegatedProjectOwner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DelegatedProjectOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = DelegatedProjectOwner::tableName();
        $projectOwner = $this->project_owner;

        $rules = [
            'project_owner' => 'bail|required|uuid|exists:project_owners,uuid',
        ];

        if ($this->isMethod('put')) {
            $delegatedProjectOwner = $this->route('delegatedProjectOwner');
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(DelegatedProjectOwner::tableName(), 'name')
                        ->ignore($delegatedProjectOwner->id)
                        ->where(fn($query) => $query->where('project_owner_uuid', $projectOwner)),
                ],
                'email' => 'bail|nullable|email|max:150|unique:' . $table . ',email,' . $delegatedProjectOwner->id,
                'phone' => 'bail|nullable|string|max:30|unique:' . $table . ',phone,' . $delegatedProjectOwner->id,
            ];
        } else {
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique(DelegatedProjectOwner::tableName(), 'name')
                        ->where(fn($query) => $query->where('project_owner_uuid', $projectOwner)),
                ],
                'email' => 'bail|nullable|email|max:150|unique:' . $table . ',email',
                'phone' => 'bail|nullable|string|max:30|unique:' . $table . ',phone',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/delegated_project_owner.request.name'),
            'project_owner_uuid' => __('app/settings/delegated_project_owner.request.project_owner'),
            'email' => __('app/settings/delegated_project_owner.request.email'),
            'phone' => __('app/settings/delegated_project_owner.request.phone'),
        ];
    }
}
