<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ActivityState;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityStateResource extends JsonResource
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
            'activity_uuid' => $this->activity_uuid,
            'state' => ActivityState::get($this->state_code, app()->getLocale()),
            'state_date' => DateTimeFormatter::formatDatetime($this->state_date),
            'author' => $author,
        ];
    }
}
