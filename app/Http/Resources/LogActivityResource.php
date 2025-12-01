<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'list');

        return match ($mode) {
            'view' => $this->forView(),
            default => $this->forList(),
        };
    }

    private function forList(): array
    {
        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'event' => $this->event,
            'subject' => class_basename($this->subject_type) . ' (#' . $this->subject_id . ')',
            'causer' => $this->whenLoaded('causer', fn() => $this->causer?->name ?? $this->causer?->email),
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'created_at' => DateTimeFormatter::formatDatetime($this->created_at)
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'event' => $this->event,
            'causer' => $this->whenLoaded(
                'causer',
                fn() =>
                $this->causer
                    ? ($this->causer->name ?? $this->causer->email) . ' (#' . $this->causer->id . ')'
                    : null
            ),
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'properties' => $this->properties,
            'created_at' => DateTimeFormatter::formatDatetime($this->created_at)
        ];
    }
}
