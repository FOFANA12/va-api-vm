<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\PriorityLevel;
use App\Support\RiskLevel;
use App\Support\StrategicObjectiveStatus;
use App\Support\StrategicState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StrategicObjectiveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
            'list' => $this->forList(),
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    protected function forList(): array
    {

        $currentLang = app()->getLocale();
        $data = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'start_date' => DateTimeFormatter::formatDate($this->start_date),
            'end_date' => DateTimeFormatter::formatDate($this->end_date),
            'priority' => PriorityLevel::get($this->priority, $currentLang),
            'risk_level' => RiskLevel::get($this->risk_level, $currentLang),
            'lead_structure' => $this->lead_structure,
            'structure' => $this->structure,
            'status' => StrategicObjectiveStatus::get($this->status, $currentLang),
            'state' => StrategicState::get($this->state, $currentLang),
        ];

        if ($this->obj_id) {
            $data['obj_id'] = $this->obj_id;
        }

        return $data;
    }

    protected function forEdit(): array
    {
        $currentLang = app()->getLocale();
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'structure' => $this->structure_uuid,
            'strategic_map' => $this->strategic_map_uuid,
            'strategic_element' => $this->strategic_element_uuid,
            'lead_structure' => $this->lead_structure_uuid,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'priority' => $this->priority,
            'risk_level' => $this->risk_level,
            'state' => StrategicState::get($this->state, $currentLang),
            'status' => StrategicObjectiveStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,
            'author' => $author,
        ];
    }

    protected function forView(): array
    {
        $currentLang = app()->getLocale();
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'structure' => $this->whenLoaded('structure', function () {
                return [
                    'name' => $this->structure->name,
                    'type' => $this->structure->type,
                ];
            }),
            'strategic_map' => $this->whenLoaded('strategicMap', fn() => $this->strategicMap->name),
            'strategic_element' => $this->whenLoaded('strategicElement', fn() => $this->strategicElement->name),
            'lead_structure' => $this->whenLoaded('leadStructure', fn() => $this->leadStructure->name),
            'start_date' => DateTimeFormatter::formatDate($this->start_date),
            'end_date' => DateTimeFormatter::formatDate($this->end_date),
            'description' => $this->description,
            'priority' => PriorityLevel::get($this->priority, $currentLang)?->label,
            'risk_level' => RiskLevel::get($this->risk_level, $currentLang)?->label,
            'state' => StrategicState::get($this->state, $currentLang),
            'status' => StrategicObjectiveStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,
            'author' => $author,
        ];
    }
}
