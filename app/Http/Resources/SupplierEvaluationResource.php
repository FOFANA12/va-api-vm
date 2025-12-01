<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierEvaluationResource extends JsonResource
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

    private function forList(): array
    {

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'score_delay' => $this->score_delay,
            'score_price' => $this->score_price,
            'score_quality' => $this->score_quality,
            'total_score' => $this->total_score,
            'comment' => $this->comment,
            'evaluated_at' => DateTimeFormatter::formatDate($this->evaluated_at)
        ];
    }

    private function forEdit(): array
    {

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'score_delay' => $this->score_delay,
            'score_price' => $this->score_price,
            'score_quality' => $this->score_quality,
            'total_score' => $this->total_score,
            'comment' => $this->comment,
            'evaluated_at' => DateTimeFormatter::formatDate($this->evaluated_at),
            'evaluated_by' => $this->whenLoaded('evaluatedBy')?->name ?? '—',
            'note' => $this->whenLoaded('supplier')?->note,
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'score_delay' => $this->score_delay,
            'score_price' => $this->score_price,
            'score_quality' => $this->score_quality,
            'total_score' => $this->total_score,
            'comment' => $this->comment,
            'evaluated_at' => DateTimeFormatter::formatDate($this->evaluated_at),
            'evaluated_by' => $this->whenLoaded('evaluatedBy')?->name ?? '—',
            'note' => $this->whenLoaded('supplier')?->note,

        ];
    }
}
