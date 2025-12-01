<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Helpers\DateTimeFormatter;
use App\Support\StrategicObjectiveStatus;
use App\Support\StrategicState;
use Illuminate\Http\Resources\Json\JsonResource;

class StrategicMapResource extends JsonResource
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
            'details' => $this->forDetails(),
            default => $this->forView(),
        };
    }

    private function forList(): array
    {
        $startDate  = DateTimeFormatter::formatDate($this->start_date);
        $endDate = DateTimeFormatter::formatDate($this->end_date);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'structure' => $this->structure,
            'status' => $this->status,
        ];
    }

    private function forEdit(): array
    {
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'structure' => $this->structure_uuid,
            'status' => $this->status,
            'author' => $this->$author,
        ];
    }

    private function forDetails(): array
    {
        $startDate  = DateTimeFormatter::formatDate($this->start_date);
        $endDate = DateTimeFormatter::formatDate($this->end_date);
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'start_date'  => $startDate,
            'end_date' => $endDate,
            'structure' => $this->whenLoaded('structure', function () {
                return [
                    'name' => $this->structure->name,
                    'abbreviation' => $this->structure->abbreviation,
                    'type' => $this->structure->type,
                ];
            }),
            'status' => $this->status,
            'author' => $this->$author,
            'elements' => $this->whenLoaded('elements', function () {
                return $this->elements->map(function ($elt) {
                    return [
                        'id' => $elt->id,
                        'uuid' => $elt->uuid,
                        'order' => $elt->order,
                        'abbreviation' => $elt->abbreviation,
                        'name' => $elt->name,
                        'objectives' => $elt->objectives->map(function ($objective) {
                            return [
                                'id' => $objective->id,
                                'uuid' => $objective->uuid,
                                'reference' => $objective->reference,
                                'name' => $objective->name,
                                'start_date' => DateTimeFormatter::formatDate($objective->start_date),
                                'end_date' => DateTimeFormatter::formatDate($objective->end_date),
                                'status' => StrategicObjectiveStatus::get($objective->status, app()->getLocale()),
                                'state' => StrategicState::get($objective->state, app()->getLocale()),
                            ];
                        })->values(),
                    ];
                });
            }),

        ];
    }

    private function forView(): array
    {
        $startDate  = DateTimeFormatter::formatDate($this->start_date);
        $endDate = DateTimeFormatter::formatDate($this->end_date);
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'start_date'  => $startDate,
            'end_date' => $endDate,
            'structure' => $this->whenLoaded('structure', fn() => $this->structure->name),
            'status' => $this->status,
            'author' => $this->$author,
        ];
    }
}
