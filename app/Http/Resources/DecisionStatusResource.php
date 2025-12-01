<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Support\DecisionStatus;
use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class DecisionStatusResource extends JsonResource
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
            default => $this->forView(),
        };
    }

    protected function forList(): array
    {
        $currentLang = app()->getLocale();
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'status_date' => DateTimeFormatter::formatDatetime($this->status_date),
            'comment' => $this->comment,
            'status' => DecisionStatus::get($this->status, $currentLang),
            'user' => $this->user,
            'download_url'  => $this->attachment_id
                ? URL::route('attachments.download', ['attachment' => $this->attachment_id])
                : null,
        ];
    }

    protected function forView(): array
    {
        $author = $this->author['name'] ?? null;
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'status_date' => DateTimeFormatter::formatDatetime($this->status_date),
            'comment' => $this->comment,
            'status' => DecisionStatus::get($this->status, $currentLang),
            'author' => $author,
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),
        ];
    }
}
