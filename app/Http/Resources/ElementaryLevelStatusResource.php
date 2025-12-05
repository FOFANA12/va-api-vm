<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ElementaryLevelStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ElementaryLevelStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'elementary_level_uuid' => $this->elementary_level_uuid,
            'status' => ElementaryLevelStatus::get($this->status_code, app()->getLocale()),
            'status_date' => DateTimeFormatter::formatDatetime($this->status_date),
            'author' => $author,
        ];
    }
}
