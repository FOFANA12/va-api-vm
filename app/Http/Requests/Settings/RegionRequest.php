<?php

namespace App\Http\Requests\Settings;

use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
            'latitude' => 'bail|nullable|numeric|between:-90,90',
            'longitude' => 'bail|nullable|numeric|between:-180,180',
        ];

        if ($this->isMethod('put')) {
            $region = $this->route('region');

            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Region::tableName() . ',name,' . $region->id,

            ];
        } else {
            $rules += [
                'name' => 'bail|required|string|max:50|unique:' . Region::tableName() . ',name',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/region.request.name'),
            'latitude' => __('app/settings/region.request.latitude'),
            'longitude' => __('app/settings/region.request.longitude'),
        ];
    }
}
