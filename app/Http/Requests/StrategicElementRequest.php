<?php

namespace App\Http\Requests;

use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\Structure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StrategicElementRequest extends FormRequest
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
        $strategicMap = $this->strategic_map;
        $type = $this->input('type', 'AXIS');

        $rules = [
            'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
            'strategic_map' => 'bail|required|exists:' . StrategicMap::tableName() . ',uuid',
            'type' => 'bail|required|in:AXIS,LEVER',
            'description' => 'bail|nullable|string|max:1000',
        ];

        if ($this->isMethod('put')) {
            $strategicElement = $this->route('strategic_element');

            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:200',
                    Rule::unique(StrategicElement::tableName(), 'name')
                        ->ignore($strategicElement->id)
                        ->where(
                            fn($query) =>
                            $query->where('strategic_map_uuid', $strategicMap)
                                ->where('type', $type)
                        ),
                ],
                'abbreviation' => [
                    'bail',
                    'required',
                    'string',
                    'max:20',
                    Rule::unique(StrategicElement::tableName(), 'abbreviation')
                        ->ignore($strategicElement->id)
                        ->where(
                            fn($query) =>
                            $query->where('strategic_map_uuid', $strategicMap)
                                ->where('type', $type)
                        ),
                ],
                'order' => [
                    'bail',
                    'required',
                    'integer',
                    'min:0',
                    Rule::unique(StrategicElement::tableName(), 'order')
                        ->ignore($strategicElement->id)
                        ->where(
                            fn($query) =>
                            $query->where('strategic_map_uuid', $strategicMap)
                                ->where('type', $type)
                        ),
                ],
            ];
        } else {

            if ($type === 'AXIS') {
                $rules['parent_structure'] = 'bail|required|exists:' . Structure::tableName() . ',uuid';
                $rules['parent_map'] = 'bail|required|exists:' . StrategicMap::tableName() . ',uuid';
                $rules['parent_element'] = 'bail|required|exists:' . StrategicElement::tableName() . ',uuid';
            }

            $rules += [
                'name' => [
                    'bail',
                    'required',
                    'string',
                    'max:200',
                    Rule::unique(StrategicElement::tableName(), 'name')
                        ->where(
                            fn($query) =>
                            $query->where('strategic_map_uuid', $strategicMap)
                                ->where('type', $type)
                        ),
                ],
                'abbreviation' => [
                    'bail',
                    'required',
                    'string',
                    'max:20',
                    Rule::unique(StrategicElement::tableName(), 'abbreviation')
                        ->where(
                            fn($query) =>
                            $query->where('strategic_map_uuid', $strategicMap)
                                ->where('type', $type)
                        ),
                ],
                'order' => [
                    'bail',
                    'required',
                    'integer',
                    'min:0',
                    Rule::unique(StrategicElement::tableName(), 'order')
                        ->where(
                            fn($query) =>
                            $query->where('strategic_map_uuid', $strategicMap)
                                ->where('type', $type)
                        ),
                ],
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'structure' => __('app/strategic_element.request.structure'),
            'strategic_map' => __('app/strategic_element.request.strategic_map'),
            'type' => __('app/strategic_element.request.type'),
            'order' => __('app/strategic_element.request.order'),
            'description' => __('app/strategic_element.request.description'),
            'abbreviation' => __('app/strategic_element.request.abbreviation'),
            'name' => __('app/strategic_element.request.name'),

            'parent_structure' => __('app/strategic_element.request.parent_structure'),
            'parent_map' => __('app/strategic_element.request.parent_map'),
            'parent_element' => __('app/strategic_element.request.parent_element'),
        ];
    }
}
