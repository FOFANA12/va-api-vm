<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ProjectStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectStatusResource extends JsonResource
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
            'project_uuid' => $this->project_uuid,
            'status' => ProjectStatus::get($this->status_code, app()->getLocale()),
            'status_date' => DateTimeFormatter::formatDatetime($this->status_date),
            'author' => $author,
        ];
    }
}
