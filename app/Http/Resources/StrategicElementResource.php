<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StrategicElementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
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
            'order' => $this->order,
            'structure' => $this->structure,
            'strategic_map' => $this->strategic_map,
            'abbreviation' => $this->abbreviation,
            'parent' => $this->parent,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'order' => $this->order,
            'type' => $this->type,
            'abbreviation' => $this->abbreviation,
            'name' => $this->name,
            'description' => $this->description,
            'structure' => $this->structure_uuid,
            'strategic_map' => $this->strategic_map_uuid,
            'status' => $this->status,
            'parent_structure' => $this->when(
                $this->type === 'AXIS' && $this->parent_structure_uuid,
                fn() => [
                    'uuid' => $this->parent_structuparent_structure_uuidre_uuid,
                    'name' => $this->parentStructure?->name,
                ]
            ),
            'parent_map' => $this->when(
                $this->type === 'AXIS' && $this->parent_map_uuid,
                fn() => [
                    'uuid' => $this->parent_map_uuid,
                    'name' => $this->parentMap?->name,
                ]
            ),
            'parent_element' => $this->when(
                $this->type === 'AXIS' && $this->parent_element_uuid,
                fn() => [
                    'uuid' => $this->parent_element_uuid,
                    'name' => $this->parent?->name,
                ]
            ),
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'order' => $this->order,
            'type' => $this->type,
            'abbreviation' => $this->abbreviation,
            'name' => $this->name,
            'description' => $this->description,
            'structure' => $this->whenLoaded('structure', fn() => $this->structure->name),
            'strategic_map' => $this->whenLoaded('strategicMap', fn() => $this->strategicMap->name),
            'status' => $this->status,
            'parent_structure' => $this->when(
                $this->type === 'AXIS' && $this->parent_structure_uuid,
                fn() => $this->parentStructure?->name
            ),

            'parent_map' => $this->when(
                $this->type === 'AXIS' && $this->parent_map_uuid,
                fn() => $this->parentMap?->name
            ),

            'parent_element' => $this->when(
                $this->type === 'AXIS' && $this->parent_element_uuid,
                fn() => $this->parent?->name
            ),
        ];
    }
}
