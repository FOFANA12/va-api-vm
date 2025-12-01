<?php

namespace App\Http\Requests\Settings;

use App\Models\FundingSource;
use App\Models\Structure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FundingSourceRequest extends FormRequest
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
        $structure = $this->structure;

        $rules = [
            'description' => 'nullable|string|max:100',
            'structure' => 'nullable|exists:' . Structure::tableName() . ',uuid',
        ];

        if ($this->isMethod('put')) {
            $fundingSource = $this->route('funding_source');
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'max:100',
                    Rule::unique(FundingSource::tableName(), 'name')
                        ->ignore($fundingSource->id)
                        ->where(fn($query) => $query->where('structure_uuid', $structure)),
                ],
            ];
        } else {
            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'max:100',
                    Rule::unique(FundingSource::tableName(), 'name')
                        ->where(fn($query) => $query->where('structure_uuid', $structure)),
                ],
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
            'name' => __('app/settings/funding_source.request.name'),
            'description' => __('app/settings/funding_source.request.description'),
            'structure' => __('app/settings/funding_source.request.structure'),
        ];
    }
}
