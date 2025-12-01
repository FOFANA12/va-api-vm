<?php
namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectOwnerResource extends JsonResource
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
            'type' => $this->type,
            'structure' => $this->structure,
            'status' => $this->status,
        ];
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'structure' => $this->structure_uuid,
            'name' => $this->name,
            'type' => $this->type,
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
            'type' => $this->type,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'structure' => $this-> whenLoaded('structure', fn() => $this->structure?->name),
        ];
    }
}
