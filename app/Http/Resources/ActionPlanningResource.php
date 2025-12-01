<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\FrequencyUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionPlanningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    protected function forEdit(): array
    {
        return [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'frequency_unit' => $this->frequency_unit,
            'frequency_value' => $this->frequency_value,
            'is_planned' => $this->is_planned,
            'periods' => $this->whenLoaded(
                'periods',
                fn($periods) =>
                $periods->map(fn($period) => [
                    'id' => $period->id,
                    'uuid' => $period->uuid,
                    'start_date' => $period->start_date,
                    'end_date' => $period->end_date,
                    'progress_percent' => $period->progress_percent,
                    'actual_progress_percent' => $period->actual_progress_percent,
                ])->values()
            ),
        ];
    }

    protected function forView(): array
    {
        $currentLang = app()->getLocale();

        return [
            'start_date' => $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null,
            'end_date' => $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null,
            'budget' => $this->budget,
            'frequency_unit' => $this->frequency_unit ? FrequencyUnit::name($this->frequency_unit, $currentLang) : null,
            'frequency_value' => $this->frequency_value,
            'is_planned' => $this->is_planned,
            'periods' => $this->whenLoaded(
                'periods',
                fn($periods) =>
                $periods->map(fn($period) => [
                    'id' => $period->id,
                    'uuid' => $period->uuid,
                    'start_date' => $period->start_date,
                    'end_date' => $period->end_date,
                    'progress_percent' => $period->progress_percent,
                    'actual_progress_percent' => $period->actual_progress_percent,
                ])->values()
            ),
        ];
    }
}
