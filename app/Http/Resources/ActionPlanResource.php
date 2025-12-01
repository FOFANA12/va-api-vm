<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ActionPlanResource extends JsonResource
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
        $startDate = $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null;
        $endDate = $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'reference' => $this->reference,
            'structure' => $this->structure,
            'responsible' => $this->responsible,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->status,
            'export_excel_url'  => $this->id
                ? URL::route('actionPlan.exportToExcel', ['actionPlan' => $this->id])
                : null,
        ];
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'reference' => $this->reference,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'structure' => $this->structure_uuid,
            'responsible' => $this->responsible_uuid,
            'status' => $this->status,
        ];
    }

    private function forView(): array
    {
        $startDate = $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null;
        $endDate = $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'reference' => $this->reference,
            'description' => $this->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'structure' => $this->whenLoaded('structure', fn() => $this->structure?->name),
            'responsible' => $this->whenLoaded('responsible', fn() => $this->responsible?->name),
            'status' => $this->status,
        ];
    }
}
