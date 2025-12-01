<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\IndicatorStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLang = app()->getLocale();
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'status' => IndicatorStatus::get($this->status_code, $currentLang),

            'status_date' => DateTimeFormatter::formatDatetime($this->status_date),
            'created_at' => DateTimeFormatter::formatDatetime($this->created_at),
            'author' => $author,
        ];
    }
}
