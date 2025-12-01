<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\TaskPriority;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return match ($request->mode) {
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    protected function forEdit(): array
    {
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'priority' => $this->priority,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'assigned_to' => $this->assigned_to,
            'deliverable' => $this->deliverable,
            'author' => $author,
        ];
    }

    protected function forView(): array
    {
        $author = $this->author['name'] ?? null;
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'priority' => TaskPriority::get($this->priority, $currentLang),
            'start_date' => DateTimeFormatter::formatDate($this->start_date),
            'end_date' => DateTimeFormatter::formatDate($this->end_date),
            'assigned_to' => $this->whenLoaded('assignedTo', fn() => $this->assignedTo?->name),
            'deliverable' => $this->deliverable,
            'author' => $author,
        ];
    }
}
