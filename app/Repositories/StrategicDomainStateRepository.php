<?php

namespace App\Repositories;

use App\Http\Resources\StrategicDomainStateResource;
use App\Models\StrategicDomainState as ModelsStrategicDomainState;
use App\Models\StrategicDomain;
use App\Support\StrategicDomainState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StrategicDomainStateRepository
{
    /**
     * List all states for a given project.
     */
    public function index($strategicDomainId)
    {
        $query = ModelsStrategicDomainState::where('strategic_domain_id', $strategicDomainId)
            ->orderByDesc('created_at');

        return StrategicDomainStateResource::collection($query->get());
    }

    /**
     * Retrieve available project states with localized labels.
     */
    public function requirements(StrategicDomain $strategicDomain)
    {
        $current = $strategicDomain->state;
        $next = StrategicDomainState::next($current);

        return [
            'states' => collect($next)->map(function ($code) {
                $state = StrategicDomainState::get($code, app()->getLocale());
                return [
                    'code'  => $state->code,
                    'name'  => $state->label,
                    'color' => $state->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new project state.
     */
    public function store(Request $request, StrategicDomain $strategicDomain)
    {
        DB::beginTransaction();
        try {
            $stateCode = $request->input('state');

            $state = ModelsStrategicDomainState::create([
                'strategic_domain_uuid' => $strategicDomain->uuid,
                'strategic_domain_id' => $strategicDomain->id,
                'state_code' => $stateCode,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $strategicDomain->update([
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            return new StrategicDomainStateResource($state);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple project states.
     */
    public function destroy(Request $request, StrategicDomain $strategicDomain)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $strategicDomain->states()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastState = $strategicDomain->states()->latest('created_at')->first();

            if ($lastState) {
                $strategicDomain->update([
                    'state' => $lastState->state_code,
                    'state_changed_at' => $lastState->state_date,
                    'state_changed_by' => $lastState->updated_by ?? $lastState->created_by,
                ]);
            } else {
                $strategicDomain->update([
                    'state' => null,
                    'state_changed_at' => null,
                    'state_changed_by' => null,
                ]);
            }

            DB::commit();

            return new StrategicDomainStateResource($lastState);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
