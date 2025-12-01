<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcurementModeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return match ($request->mode) {
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
            'name' => $this->name,
            'status' => $this->status,
            'contract_type' => $this->contract_type,
        ];
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'contract_type' => $this->contract_type_uuid,
            'duration' => $this->duration,
            'status' => $this->status,
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'duration' => $this->duration,
            'contract_type' => $this->whenLoaded('contractType', fn() => $this->contractType?->name),
            'status' => $this->status,
        ];
    }
}
