<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\StrategicObjectiveStatus;
use App\Support\StrategicState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatrixPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mode = $request->query('mode', 'view');
        return match ($mode) {
            'details' => $this->forDetails(),
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    protected function forDetails(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'start_date' => DateTimeFormatter::formatDate($this->start_date),
            'end_date' => DateTimeFormatter::formatDate($this->end_date),
            'strategic_objectives' => $this->whenLoaded(
                'strategicObjectives',
                fn() => $this->strategicObjectives->map(fn($obj) => [
                    'id' => $obj->id,
                    'uuid' => $obj->uuid,
                    'reference' => $obj->reference,
                    'start_date' => DateTimeFormatter::formatDate($obj->start_date),
                    'end_date' => DateTimeFormatter::formatDate($obj->end_date),
                    'name' => $obj->name,
                    'status' => StrategicObjectiveStatus::get($obj->status, app()->getLocale()),
                    'state' => StrategicState::get($obj->state, app()->getLocale()),
                ])
            ),
        ];
    }

    protected function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }

    protected function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'start_date' => DateTimeFormatter::formatDate($this->start_date),
            'end_date' => DateTimeFormatter::formatDate($this->end_date),
        ];
    }
}
