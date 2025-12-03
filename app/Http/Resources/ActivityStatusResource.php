<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ActivityStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityStatusResource extends JsonResource
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
            'capability_domain_uuid' => $this->capability_domain_uuid,
            'status' => ActivityStatus::get($this->status_code, app()->getLocale()),
            'status_date' => DateTimeFormatter::formatDatetime($this->status_date),
            'author' => $author,
        ];
    }
}
