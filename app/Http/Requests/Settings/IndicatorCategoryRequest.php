<?php

namespace App\Http\Requests\Settings;
use App\Models\IndicatorCategory;
use Illuminate\Foundation\Http\FormRequest;

class IndicatorCategoryRequest extends FormRequest
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
            'name'  => 'bail|required|string|max:100|unique:' . IndicatorCategory::tableName() . ',name',
        ];

        if ($this->isMethod('put')) {
            $indicatorCategory = $this->route('indicator_category');

            $rules['name']  = 'bail|required|string|max:100|unique:' . IndicatorCategory::tableName() . ',name,' . $indicatorCategory->id;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('app/settings/indicator_category.request.name'),
        ];
    }
}
