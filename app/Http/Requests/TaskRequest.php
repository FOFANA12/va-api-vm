<?php

namespace App\Http\Requests;

use App\Helpers\DateTimeFormatter;
use App\Models\User;
use App\Support\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
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
            'title' => 'bail|required|string|max:150',
            'description' => 'bail|nullable|string|max:1000',
            'priority' => ['bail', 'required', Rule::in(TaskPriority::codes())],
            'start_date' => 'bail|required|date',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'assigned_to' => 'bail|nullable|exists:' . User::tableName() . ',uuid',
            'deliverable' => 'bail|nullable|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('post')) {
                $phase = $this->route('action_phase');
            }

            if ($this->isMethod('put')) {
                $task = $this->route('task');
                $phase = $task?->phase;
            }

            if (isset($phase)) {
                $phaseStart = $phase->start_date;
                $phaseEnd = $phase->end_date;

                if ($this->start_date < $phaseStart || $this->end_date > $phaseEnd) {
                    $validator->errors()->add(
                        'invalid_date_range',
                        __('app/task.request.invalid_date_range', [
                            'start' => DateTimeFormatter::formatDate($phaseStart),
                            'end' => DateTimeFormatter::formatDate($phaseEnd),
                        ])
                    );
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'title' => __('app/task.request.title'),
            'description' => __('app/task.request.description'),
            'priority' => __('app/task.request.priority'),
            'start_date' => __('app/task.request.start_date'),
            'end_date' => __('app/task.request.end_date'),
            'assigned_to' => __('app/task.request.assigned_to'),
            'deliverable' => __('app/task.request.deliverable'),
        ];
    }
}
