<?php

namespace App\Http\Resources;

use App\Support\StructureType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class StructureResource extends JsonResource
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
            'abbreviation' => $this->abbreviation,
            'name' => $this->name,
            'parent' => $this->parent,
            'status' => $this->status,
            'type' => StructureType::get($this->type, app()->getLocale()),
            'export_action_plan_url'  => $this->id
                ? URL::route('structure.exportActionPlanToExcel', ['structure' => $this->id])
                : null,
            'export_bilan_url'  => $this->id
                ? URL::route('structure.exportBilanToExcel', ['structure' => $this->id])
                : null,
            'export_ppm_url' => $this->id
                ? URL::route('structure.exportProcurementPlanToWord', [
                    'structure' => $this->id,
                    'generateDocumentType' => 'ppm',
                ])
                : null,
            'export_paa_url' => $this->id
                ? URL::route('structure.exportProcurementPlanToWord', [
                    'structure' => $this->id,
                    'generateDocumentType' => 'paa',
                ])
                : null,
            'export_objective_url' => $this->id
                ? URL::route('structure.exportObjectiveToWord', [
                    'structure' => $this->id,
                ])
                : null,
            'export_objective_decision_url' => $this->id
                ? URL::route('structure.exportObjectiveDecisionToWord', [
                    'structure' => $this->id,
                ])
                : null,
        ];
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'abbreviation' => $this->abbreviation,
            'name' => $this->name,
            'parent' => $this->parent_uuid,
            'status' => $this->status,
            'type' => StructureType::get($this->type, app()->getLocale()),
        ];
    }

    private function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'abbreviation' => $this->abbreviation,
            'name' => $this->name,
            'parent' => $this->whenLoaded('parent', fn() => $this->parent?->name),
            'status' => $this->status,
            'type' => StructureType::get($this->type, app()->getLocale()),
        ];
    }
}
