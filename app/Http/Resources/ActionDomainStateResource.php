<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ActionDomainState;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionDomainStateResource extends JsonResource
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
            'state' => ActionDomainState::get($this->state_code, app()->getLocale()),
            'state_date' => DateTimeFormatter::formatDatetime($this->state_date),
            'author' => $author,
        ];
    }
}
