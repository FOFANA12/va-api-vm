<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ChartType;
use App\Support\FrequencyUnit;
use App\Support\IndicatorStatus;
use App\Support\StrategicState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
            'list' => $this->forList(),
            'edit', 'copy' => $this->forEdit($mode),
            default => $this->forView(),
        };
    }

    protected function forList(): array
    {
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'lead_structure' => $this->lead_structure,
            'structure' => $this->structure,
            'reference' => $this->reference,
            'chart_type' => ChartType::get($this->chart_type, $currentLang),
            'initial_value' => $this->initial_value,
            'final_target_value' => $this->final_target_value,
            'achieved_value' => $this->achieved_value,
            'unit' => $this->unit,
            'is_planned' => $this->is_planned,
            'status' => IndicatorStatus::get($this->status, $currentLang),
            'state' => StrategicState::get($this->state, $currentLang),
        ];
    }

    protected function forEdit($mode): array
    {
        $author = $this->author['name'] ?? null;
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'structure' => $this->whenLoaded('structure', function ($structure) use ($mode) {
                if ($mode === 'copy') {
                    return $structure->uuid;
                }

                return [
                    'name' => $structure->name,
                    'type' => $structure->type,
                ];
            }),
            'strategic_map' => $this->whenLoaded(
                'strategicMap',
                fn($map) =>
                $mode === 'copy'
                    ? $map->uuid
                    : $map->name
            ),
            'strategic_element' => $this->whenLoaded(
                'strategicElement',
                fn($elt) =>
                $mode === 'copy'
                    ? $elt?->uuid
                    : $elt?->name
            ),
            'strategic_objective' => $this->whenLoaded(
                'strategicObjective',
                fn($objective) =>
                $mode === 'copy'
                    ? $objective?->uuid
                    : $objective?->name
            ),
            'category' => $this->category_uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'description' => $this->description,
            'chart_type' => $this->chart_type,
            'frequency_unit' => $this->frequency_unit,
            'frequency_value' => $this->frequency_value,
            'initial_value' => $this->initial_value,
            'final_target_value' => $this->final_target_value,
            'achieved_value' => $this->achieved_value,
            'unit' => $this->unit,
            'state' => StrategicState::get($this->state, $currentLang),
            'status' => IndicatorStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->whenLoaded('statusChangedBy')?->name,
            'author' => $author,
            'is_planned' => $this->is_planned,
        ];
    }

    protected function forView(): array
    {
        $currentLang = app()->getLocale();
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'structure' => $this->whenLoaded('structure', function () {
                return [
                    'name' => $this->structure->name,
                    'type' => $this->structure->type,
                ];
            }),
            'strategic_map' => $this->whenLoaded('strategicMap')->name,
            'strategic_element' => $this->whenLoaded('strategicElement')?->name,
            'strategic_objective' => $this->whenLoaded('strategicObjective')->name,
            'category' => $this->category_uuid ?  $this->whenLoaded('category')?->name : null,
            'reference' => $this->reference,
            'name' => $this->name,
            'description' => $this->description,
            'chart_type' => ChartType::name($this->chart_type, $currentLang),
            'frequency_unit' => $this->frequency_unit ? FrequencyUnit::name($this->frequency_unit) : null,
            'frequency_value' => $this->frequency_value,
            'initial_value' => $this->initial_value,
            'final_target_value' => $this->final_target_value,
            'achieved_value' => $this->achieved_value,
            'unit' => $this->unit,
            'state' => StrategicState::get($this->state, $currentLang),
            'status' => IndicatorStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->whenLoaded('statusChangedBy')?->name,
            'author' => $author,
            'is_planned' => $this->is_planned,
        ];
    }
}
