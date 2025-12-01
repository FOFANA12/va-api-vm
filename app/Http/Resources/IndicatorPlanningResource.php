<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\FrequencyUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorPlanningResource extends JsonResource
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
        $objective = $this->whenLoaded('strategicObjective');

        return [
            'start_date' => $objective?->start_date ? $objective->start_date : null,
            'end_date' => $objective?->end_date ? $objective->end_date : null,
            'initial_value' => $this->initial_value,
            'final_target_value' => $this->final_target_value,
            'unit' => $this->unit,
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
                    'target_value' => $period->target_value,
                    'achieved_value' => $period->achieved_value,
                ])->values()
            ),
        ];
    }

    protected function forView(): array
    {
        $currentLang = app()->getLocale();
        $objective = $this->whenLoaded('strategicObjective');

        return [
            'start_date' => $objective?->start_date ? DateTimeFormatter::formatDate($objective->start_date) : null,
            'end_date' => $objective?->end_date ? DateTimeFormatter::formatDate($objective->end_date) : null,
            'initial_value' => $this->initial_value,
            'final_target_value' => $this->final_target_value,
            'unit' => $this->unit,
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
                    'target_value' => $period->target_value,
                    'achieved_value' => $period->achieved_value,
                ])->values()
            ),
        ];
    }
}
