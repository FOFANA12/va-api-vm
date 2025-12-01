<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DelegatedProjectOwnerResource extends JsonResource
{
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
            'email' => $this->email,
            'phone' => $this->phone,
            'project_owner' => $this->projectOwner->name,
            'status' => $this->status,
        ];
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'project_owner' => $this->project_owner_uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'project_owner' => $this-> whenLoaded('projectOwner', fn() => $this->projectOwner?->name),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
