<?php

namespace App\Repositories;

use App\Http\Resources\CapabilityDomainStateResource;
use App\Models\CapabilityDomainState as ModelsCapabilityDomainState;
use App\Models\CapabilityDomain;
use App\Support\CapabilityDomainState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CapabilityDomainStateRepository
{
    /**
     * List all states for a given capability domain.
     */
    public function index($capabilityDomainId)
    {
        $query = ModelsCapabilityDomainState::where('capability_domain_id', $capabilityDomainId)
            ->orderByDesc('created_at');

        return CapabilityDomainStateResource::collection($query->get());
    }

    /**
     * Retrieve available capability domain states with localized labels.
     */
    public function requirements(CapabilityDomain $capabilityDomain)
    {
        $current = $capabilityDomain->state;
        $next = CapabilityDomainState::next($current);

        return [
            'states' => collect($next)->map(function ($code) {
                $state = CapabilityDomainState::get($code, app()->getLocale());
                return [
                    'code'  => $state->code,
                    'name'  => $state->label,
                    'color' => $state->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new capability domain state.
     */
    public function store(Request $request, CapabilityDomain $capabilityDomain)
    {
        DB::beginTransaction();
        try {
            $stateCode = $request->input('state');

            $state = ModelsCapabilityDomainState::create([
                'capability_domain_uuid' => $capabilityDomain->uuid,
                'capability_domain_id' => $capabilityDomain->id,
                'state_code' => $stateCode,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $capabilityDomain->update([
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            return new CapabilityDomainStateResource($state);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple capability domain states.
     */
    public function destroy(Request $request, CapabilityDomain $capabilityDomain)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $capabilityDomain->states()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastState = $capabilityDomain->states()->latest('created_at')->first();

            if ($lastState) {
                $capabilityDomain->update([
                    'state' => $lastState->state_code,
                    'state_changed_at' => $lastState->state_date,
                    'state_changed_by' => $lastState->updated_by ?? $lastState->created_by,
                ]);
            } else {
                $capabilityDomain->update([
                    'state' => null,
                    'state_changed_at' => null,
                    'state_changed_by' => null,
                ]);
            }

            DB::commit();

            return new CapabilityDomainStateResource($lastState);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
