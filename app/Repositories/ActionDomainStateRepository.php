<?php

namespace App\Repositories;

use App\Http\Resources\ActionDomainStateResource;
use App\Models\ActionDomain;
use App\Models\ActionDomainState as ModelsActionDomainState;
use App\Support\ActionDomainState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActionDomainStateRepository
{
    /**
     * List all states for a given action domain.
     */
    public function index($actionDomainId)
    {
        $query = ModelsActionDomainState::where('action_domain_id', $actionDomainId)
            ->orderByDesc('created_at');

        return ActionDomainStateResource::collection($query->get());
    }

    /**
     * Retrieve available action domain states with localized labels.
     */
    public function requirements(ActionDomain $actionDomain)
    {
        $current = $actionDomain->state;
        $next = ActionDomainState::next($current);

        return [
            'states' => collect($next)->map(function ($code) {
                $state = ActionDomainState::get($code, app()->getLocale());
                return [
                    'code'  => $state->code,
                    'name'  => $state->label,
                    'color' => $state->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new program state.
     */
    public function store(Request $request, ActionDomain $actionDomain)
    {
        DB::beginTransaction();
        try {
            $stateCode = $request->input('state');

            $state = ModelsActionDomainState::create([
                'action_domain_uuid' => $actionDomain->uuid,
                'action_domain_id' => $actionDomain->id,
                'state_code' => $stateCode,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionDomain->update([
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            return new ActionDomainStateResource($state);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple program states.
     */
    public function destroy(Request $request, ActionDomain $actionDomain)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $actionDomain->states()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastState = $actionDomain->states()->latest('created_at')->first();

            if ($lastState) {
                $actionDomain->update([
                    'state' => $lastState->state_code,
                    'state_changed_at' => $lastState->state_date,
                    'state_changed_by' => $lastState->updated_by ?? $lastState->created_by,
                ]);
            } else {
                $actionDomain->update([
                    'state' => null,
                    'state_changed_at' => null,
                    'state_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ActionDomainStateResource($lastState);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
