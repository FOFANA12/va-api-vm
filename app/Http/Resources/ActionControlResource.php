<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ActionControlResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
            'list' => $this->forList(),
            default => $this->forView(),
        };
    }

    private function forList(): array
    {
        $periodStartDate = DateTimeFormatter::formatDate($this->period_start);
        $periodEndDate = DateTimeFormatter::formatDate($this->period_end);
        $controlDate = $this->control_date ? DateTimeFormatter::formatDate($this->control_date) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'period_id' => $this->period_id,
            'action_period' => $periodStartDate && $periodEndDate ? "$periodStartDate → $periodEndDate" : null,
            'progress_percent' => $this->progress_percent,
            'control_date' => $controlDate,
            'file' => null,
            'actual_progress_percent' => $this->actual_progress_percent,
            'author' => $this->created_by,
            'download_url'  => $this->attachment_id
                ? URL::route('attachments.download', ['attachment' => $this->attachment_id])
                : null,
        ];
    }

    private function forView(): array
    {
        $period = $this->whenLoaded('actionPeriod');
        $startDate = $period ? DateTimeFormatter::formatDate($period->start_date) : null;
        $endDate = $period ? DateTimeFormatter::formatDate($period->end_date) : null;
        $controlDate = DateTimeFormatter::formatDate($this->control_date);
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'action_period' => $startDate . ' → ' . $endDate,
            'root_cause' => $this->root_cause,
            'control_date' => $controlDate,
            'forecast_percent' => $this->forecast_percent,
            'actual_progress_percent' => $this->actual_progress_percent,
            'author' => $author,
            'phases' => $this->whenLoaded('controlPhases', function ($controlPhases) {
                return $controlPhases->map(function ($controlPhase) {
                    return [
                        'id' => $controlPhase->id,
                        'uuid' => $controlPhase->uuid,
                        'phase' => [
                            'name' => $controlPhase->phase?->name,
                            'start_date' => DateTimeFormatter::formatDate($controlPhase->phase?->start_date),
                            'end_date' => DateTimeFormatter::formatDate($controlPhase->phase?->end_date),
                        ],
                        'progress_percent' => $controlPhase->progress_percent,
                        'weight' => $controlPhase->weight,
                    ];
                })->values();
            }),
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),
        ];
    }
}
