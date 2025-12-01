<?php

namespace App\Http\Requests;

use App\Models\Structure;
use App\Models\StrategicMap;
use Illuminate\Foundation\Http\FormRequest;

class StrategicMapRequest extends FormRequest
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
        return [
            'structure' => 'bail|required|exists:' . Structure::tableName() . ',uuid',
            'name' => 'bail|required|string|max:100',
            'description' => 'bail|nullable|string|max:1500',
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (filter_var($this->status, FILTER_VALIDATE_BOOL)) {

                if ($this->isMethod('post')) {
                    $exist = StrategicMap::where('structure_uuid', $this->input('structure'))
                        ->where('status', true)
                        ->first();

                    if ($exist) {
                        $validator->errors()->add(
                            'structure',
                            __('app/strategic_map.request.already_active_strategic_map')
                        );
                    }
                }

                if ($this->isMethod('put')) {
                    $strategicMap = $this->route('strategic_map');
                    $exist = StrategicMap::where('structure_uuid', $this->input('structure'))
                        ->where('status', true)
                        ->where('id', '<>', $strategicMap->id)
                        ->first();

                    if ($exist) {
                        $validator->errors()->add(
                            'structure',
                            __('app/strategic_map.request.already_active_strategic_map')
                        );
                    }
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'structure' => __('app/strategic_map.request.structure'),
            'name' => __('app/strategic_map.request.name'),
            'description' => __('app/strategic_map.request.description'),
            'start_date' => __('app/strategic_map.request.start_date'),
            'end_date' => __('app/strategic_map.request.end_date'),
        ];
    }
}
