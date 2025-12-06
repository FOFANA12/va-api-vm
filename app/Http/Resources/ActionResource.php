<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\ActionState;
use App\Support\ActionStatus;
use App\Support\GenerateDocumentTypes;
use App\Support\PriorityLevel;
use App\Support\RiskLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
            'list' => $this->forList(),
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    protected function forList(): array
    {

        $currentLang = app()->getLocale();
        $data = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'priority' => PriorityLevel::get($this->priority, $currentLang),
            'risk_level' => RiskLevel::get($this->risk_level, $currentLang),
            'status' => ActionStatus::get($this->status, $currentLang),
            'state' => $this->state ? ActionState::get($this->state, $currentLang) : null,
            'currency' => $this->currency,
            'structure' => $this->structure,
            'project_owner' => $this->projectOwner,
            'is_planned' => $this->is_planned,
            'actual_progress_percent' => $this->actual_progress_percent,

            'start_date' => $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null,
            'end_date' => $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null,
            'total_budget' => $this->total_budget,
            'disbursement_rate' =>  $this->total_receipt_fund > 0
                ? round(($this->total_disbursement_fund / $this->total_receipt_fund) * 100, 2)
                : 0
        ];

        if ($this->action_id) {
            $data['action_id'] = $this->action_id;
        }

        return $data;
    }

    protected function forEdit(): array
    {
        $author = $this->author['name'] ?? null;
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'priority' => $this->priority,
            'risk_level' => $this->risk_level,
            'generate_document_type' => $this->generate_document_type,
            'structure' => $this->structure_uuid,
            'action_plan' => $this->action_plan_uuid,
            'project_owner' => $this->project_owner_uuid,
            'delegated_project_owner' => $this->delegated_project_owner_uuid,
            'currency' => $this->currency,
            'region' => $this->region_uuid,
            'department' => $this->department_uuid,
            'municipality' => $this->municipality_uuid,
            'action_domain' => $this->action_domain_uuid,
            'strategic_domain' => $this->strategic_domain_uuid,
            'capability_domain' => $this->capability_domain_uuid,
            'elementary_level' => $this->elementary_level_uuid,
            'description' => $this->description,
            'prerequisites' => $this->prerequisites,
            'impacts' => $this->impacts,
            'risks' => $this->risks,

            'responsible_structure' => $this->responsible_structure_uuid,
            'responsible' => $this->responsible_uuid,

            'beneficiaries' => $this->beneficiaries->map(function ($item) {
                return [
                    'uuid' => $item->uuid,
                    'name' => $item->name,
                ];
            }),
            'stakeholders' => $this->stakeholders->map(function ($item) {
                return [
                    'uuid' => $item->uuid,
                    'name' => $item->name,
                ];
            }),
            'funding_sources' => $this->fundingSources->map(function ($item) {
                return [
                    'uuid' => $item->uuid,
                    'name' => $item->name,
                    'planned_amount' => (float) $item->pivot->planned_budget,
                ];
            }),
            'status' => ActionStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,
            'state' => $this->state ? ActionState::get($this->state, $currentLang) : null,
            'author' => $author,
            'is_planned' => $this->is_planned,
            'actual_progress_percent' => $this->actual_progress_percent,
            'download_selection_mode_url'  => URL::route('templates.selection-mode.download'),
        ];
    }

    protected function forView(): array
    {
        $currentLang = app()->getLocale();
        $author = $this->author['name'] ?? null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'priority' => PriorityLevel::get($this->priority, $currentLang),
            'risk_level' => RiskLevel::get($this->risk_level, $currentLang),
            'generate_document_type' => GenerateDocumentTypes::get($this->generate_document_type, $currentLang)->label,
            'structure' => $this->structure?->name,
            'action_plan' => $this->actionPlan?->name,
            'project_owner' => $this->projectOwner?->name,
            'delegated_project_owner' => $this->delegatedProjectOwner?->name,
            'currency' => $this->currency,

            'region' => $this->region?->name,
            'department' => $this->department?->name,
            'municipality' => $this->municipality?->name,
            'action_domain' => $this->actionDomain?->name,
            'strategic_domain' => $this->strategicDomain?->name,
            'capability_domain' => $this->capabilityDomain?->name,
            'elementary_level' => $this->elementaryLevel?->name,
            'description' => $this->description,
            'prerequisites' => $this->prerequisites,
            'impacts' => $this->impacts,
            'risks' => $this->risks,
            'responsible_structure' => $this->responsibleStructure?->name,
            'responsible' => $this->responsible?->name,
            'beneficiaries' => $this->beneficiaries->map(fn($item) => ['name' => $item->name]),
            'stakeholders' => $this->stakeholders->map(fn($item) => ['name' => $item->name]),
            'funding_sources' => $this->fundingSources->map(fn($item) => [
                'name' => $item->name,
                'planned_amount' => (float) $item->pivot->planned_budget,
            ]),
            'status' => ActionStatus::get($this->status, $currentLang),
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,
            'state' => $this->state ? ActionState::get($this->state, $currentLang) : null,
            'author' => $author,
            'is_planned' => $this->is_planned,
            'actual_progress_percent' => $this->actual_progress_percent,
            'download_selection_mode_url'  => URL::route('templates.selection-mode.download'),
        ];
    }
}
