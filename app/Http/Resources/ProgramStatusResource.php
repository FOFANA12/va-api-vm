<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ProgramStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramStatusResource extends JsonResource
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
            'action_domain_uuid' => $this->action_domain_uuid,
            'status' => ProgramStatus::get($this->status_code, app()->getLocale()),
            'status_date' => DateTimeFormatter::formatDatetime($this->status_date),
            'author' => $author,
        ];
    }
}
