<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionPhaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return match ($request->mode) {
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'number' => $this->number,
            'description' => $this->description,
            'deliverable' => $this->deliverable,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'action' => $this->action_uuid,
            'weight' => $this->weight,
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'number' => $this->number,
            'description' => $this->description,
            'deliverable' => $this->deliverable,
            'start_date' => DateTimeFormatter::formatDate($this->start_date),
            'end_date' => DateTimeFormatter::formatDate($this->end_date),
            'weight' => $this->weight,
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
