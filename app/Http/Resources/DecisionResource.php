<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Support\PriorityLevel;
use App\Support\DecisionStatus;
use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class DecisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
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
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'decision_date' => DateTimeFormatter::formatDate($this->decision_date),
            'title' => $this->title,
            'description' => $this->description,
            'priority' => PriorityLevel::get($this->priority, $currentLang),
            'status' => DecisionStatus::get($this->status, $currentLang),
            'user' => $this->user,
            'download_url'  => $this->attachment_id
                ? URL::route('attachments.download', ['attachment' => $this->attachment_id])
                : null,
        ];
    }

    protected function forEdit(): array
    {
        $author = $this->author['name'] ?? null;
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'decision_date' => $this->decision_date,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => DecisionStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,
            'author' => $author,
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),
        ];
    }

    protected function forView(): array
    {
        $author = $this->author['name'] ?? null;
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'decision_date' => DateTimeFormatter::formatDate($this->decision_date),
            'title' => $this->title,
            'description' => $this->description,
            'priority' => PriorityLevel::get($this->priority, $currentLang),
            'status' => DecisionStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,
            'author' => $author,
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),
        ];
    }
}
