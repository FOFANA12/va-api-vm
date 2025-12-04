<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use App\Support\CapabilityDomainState;
use App\Support\CapabilityDomainStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityDomainResource extends JsonResource
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
        $currentLang = app()->getLocale();
        $startDate = $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null;
        $endDate = $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'strategic_domain' => $this->strategicDomain,
            'reference' => $this->reference,
            'name' => $this->name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'budget' => $this->budget,
            'currency' => $this->currency,
            'responsible' => $this->responsible,
            'status' => $this->status ? CapabilityDomainStatus::get($this->status, $currentLang) : null,
            'state' => $this->state ? CapabilityDomainState::get($this->state, $currentLang) : null,
        ];
    }

    private function forEdit(): array
    {
        $currentLang = app()->getLocale();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'strategic_domain' => $this->strategic_domain_uuid,
            'currency' => $this->currency,
            'responsible' => $this->responsible_uuid,

            'status' => $this->status ? CapabilityDomainStatus::get($this->status, $currentLang) : null,
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,

            'state' => $this->state ? CapabilityDomainState::get($this->state, $currentLang) : null,
            'state_changed_at' => $this->state_changed_at ? DateTimeFormatter::formatDatetime($this->state_changed_at) : null,
            'state_changed_by' => $this->stateChangedBy?->name,

            'description' => $this->description,
            'prerequisites' => $this->prerequisites,
            'impacts' => $this->impacts,
            'risks' => $this->risks,
            'beneficiaries' => $this->beneficiaries->map(function ($item) {
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
        ];
    }

    private function forView(): array
    {
        $currentLang = app()->getLocale();
        $startDate = $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null;
        $endDate = $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'budget' => $this->budget,
            'currency' => $this->currency,
            'strategic_domain' => $this->strategicDomain ? $this->strategicDomain->name : null,
            'responsible' => $this->responsible ? $this->responsible->name : null,

            'status' => $this->status ? CapabilityDomainStatus::get($this->status, $currentLang) : null,
            'status_changed_at' => $this->status_changed_at ? DateTimeFormatter::formatDatetime($this->status_changed_at) : null,
            'status_changed_by' => $this->statusChangedBy?->name,

            'state' => $this->state ? CapabilityDomainState::get($this->state, $currentLang) : null,
            'state_changed_at' => $this->state_changed_at ? DateTimeFormatter::formatDatetime($this->state_changed_at) : null,
            'state_changed_by' => $this->stateChangedBy?->name,

            'description' => $this->description,
            'prerequisites' => $this->prerequisites,
            'impacts' => $this->impacts,
            'risks' => $this->risks,
            'beneficiaries' => $this->beneficiaries->map(fn($item) => ['name' => $item->name]),
            'funding_sources' => $this->fundingSources->map(fn($item) => [
                'name' => $item->name,
                'planned_amount' => (float) $item->pivot->planned_budget,
            ]),
        ];
    }
}
