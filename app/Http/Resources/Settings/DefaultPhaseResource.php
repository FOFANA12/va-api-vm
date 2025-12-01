<?php

namespace App\Http\Resources\Settings;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefaultPhaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return match ($request->mode) {
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'number' => $this->number,
            'duration' => $this->duration,
            'weight' => $this->weight,
            'description' => $this->description,
            'deliverable' => $this->deliverable,
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'number' => $this->number,
            'duration' => $this->duration,
            'weight' => $this->weight,
            'author' => $this->author,
            'description' => $this->description,
            'deliverable' => $this->deliverable,
        ];
    }
}
