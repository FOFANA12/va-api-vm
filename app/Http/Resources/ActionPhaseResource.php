<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Carbon\Carbon;
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
        $start = $this->start_date ? Carbon::parse($this->start_date) : null;
        $end = $this->end_date ? Carbon::parse($this->end_date) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'number' => $this->number,
            'description' => $this->description,
            'deliverable' => $this->deliverable,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'gantt_start_date' => $start?->format('Y-m-d'),
            'gantt_end_date' => $end?->format('Y-m-d'),
            'gantt_duration' => ($start && $end) ? $start->diffInDays($end) + 1 : null,
            'action' => $this->action_uuid,
            'weight' => $this->weight,
        ];
    }

    private function forView(): array
    {
        $start = $this->start_date ? Carbon::parse($this->start_date) : null;
        $end = $this->end_date ? Carbon::parse($this->end_date) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'number' => $this->number,
            'description' => $this->description,
            'deliverable' => $this->deliverable,
            'start_date' => DateTimeFormatter::formatDate($this->start_date),
            'end_date' => DateTimeFormatter::formatDate($this->end_date),
            'gantt_start_date' => $start?->format('Y-m-d'),
            'gantt_end_date' => $end?->format('Y-m-d'),
            'gantt_duration' => ($start && $end) ? $start->diffInDays($end) + 1 : null,
            'weight' => $this->weight,
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
