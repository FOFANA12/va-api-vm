<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ElementaryLevelState;
use Illuminate\Http\Resources\Json\JsonResource;

class ElementaryLevelStateResource extends JsonResource
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
            'state' => ElementaryLevelState::get($this->state_code, app()->getLocale()),
            'state_date' => DateTimeFormatter::formatDatetime($this->state_date),
            'author' => $author,
        ];
    }
}
