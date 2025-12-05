<?php

namespace App\Repositories;

use App\Http\Resources\ElementaryLevelStateResource;
use App\Models\ElementaryLevelState as ModelsElementaryLevelState;
use App\Models\ElementaryLevel;
use App\Support\ElementaryLevelState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ElementaryLevelStateRepository
{
    /**
     * List all states for a given elementary level.
     */
    public function index($elementaryLevelId)
    {
        $query = ModelsElementaryLevelState::where('elementary_level_id', $elementaryLevelId)
            ->orderByDesc('created_at');

        return ElementaryLevelStateResource::collection($query->get());
    }

    /**
     * Retrieve available elementary level states with localized labels.
     */
    public function requirements(ElementaryLevel $elementaryLevel)
    {
        $current = $elementaryLevel->state;
        $next = ElementaryLevelState::next($current);

        return [
            'states' => collect($next)->map(function ($code) {
                $state = ElementaryLevelState::get($code, app()->getLocale());
                return [
                    'code'  => $state->code,
                    'name'  => $state->label,
                    'color' => $state->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new elementary level state.
     */
    public function store(Request $request, ElementaryLevel $elementaryLevel)
    {
        DB::beginTransaction();
        try {
            $stateCode = $request->input('state');

            $state = ModelsElementaryLevelState::create([
                'elementary_level_uuid' => $elementaryLevel->uuid,
                'elementary_level_id' => $elementaryLevel->id,
                'state_code' => $stateCode,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $elementaryLevel->update([
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            return new ElementaryLevelStateResource($state);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple elementary level states.
     */
    public function destroy(Request $request, ElementaryLevel $elementaryLevel)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $elementaryLevel->states()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastState = $elementaryLevel->states()->latest('created_at')->first();

            if ($lastState) {
                $elementaryLevel->update([
                    'state' => $lastState->state_code,
                    'state_changed_at' => $lastState->state_date,
                    'state_changed_by' => $lastState->updated_by ?? $lastState->created_by,
                ]);
            } else {
                $elementaryLevel->update([
                    'state' => null,
                    'state_changed_at' => null,
                    'state_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ElementaryLevelStateResource($lastState);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
