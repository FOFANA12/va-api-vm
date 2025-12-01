<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class IndicatorControlResource extends JsonResource
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
        $controlDate = DateTimeFormatter::formatDate($this->control_date);

        return [
            'id' => $this->id,
            'period_id' => $this->period_id,
            'uuid' => $this->uuid,
            'indicator_period' => $periodStartDate && $periodEndDate ? "$periodStartDate → $periodEndDate" : null,
            'control_date' => $controlDate,
            'achieved_value' => sprintf('%.2d %s', $this->achieved_value, $this->unit),
            'target_value' => sprintf('%.2d %s', $this->target_value, $this->unit),
            'author' => $this->created_by,
            'download_url'  => $this->attachment_id
                ? URL::route('attachments.download', ['attachment' => $this->attachment_id])
                : null,
        ];
    }

    private function forView(): array
    {
        $period = $this->whenLoaded('indicatorPeriod');
        $unit = $period->indicator->unit ?? '';
        $startDate = $period ? DateTimeFormatter::formatDate($period->start_date) : null;
        $endDate = $period ? DateTimeFormatter::formatDate($period->end_date) : null;
        $controlDate = DateTimeFormatter::formatDate($this->control_date);
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'indicator_period' => $startDate . ' → ' . $endDate,
            'root_cause' => $this->root_cause,
            'control_date' => $controlDate,
            'achieved_value' => "$this->achieved_value $unit",
            'target_value' => "$this->target_value $unit",
            'author' => $author,
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),
        ];
    }
}
