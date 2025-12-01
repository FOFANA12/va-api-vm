<?php

namespace App\Repositories;

use App\Http\Resources\ProgramStateResource;
use App\Models\Program;
use App\Models\ProgramState as ModelsProgramState;
use App\Support\ProgramState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgramStateRepository
{
    /**
     * List all states for a given program.
     */
    public function index($programId)
    {
        $query = ModelsProgramState::where('program_id', $programId)
            ->orderByDesc('created_at');

        return ProgramStateResource::collection($query->get());
    }

    /**
     * Retrieve available program states with localized labels.
     */
    public function requirements(Program $program)
    {
        $current = $program->state;
        $next = ProgramState::next($current);

        return [
            'states' => collect($next)->map(function ($code) {
                $state = ProgramState::get($code, app()->getLocale());
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
    public function store(Request $request, Program $program)
    {
        DB::beginTransaction();
        try {
            $stateCode = $request->input('state');

            $state = ModelsProgramState::create([
                'program_uuid' => $program->uuid,
                'program_id' => $program->id,
                'state_code' => $stateCode,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $program->update([
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            return new ProgramStateResource($state);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple program states.
     */
    public function destroy(Request $request, Program $program)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $program->states()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastState = $program->states()->latest('created_at')->first();

            if ($lastState) {
                $program->update([
                    'state' => $lastState->state_code,
                    'state_changed_at' => $lastState->state_date,
                    'state_changed_by' => $lastState->updated_by ?? $lastState->created_by,
                ]);
            } else {
                $program->update([
                    'state' => null,
                    'state_changed_at' => null,
                    'state_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ProgramStateResource($lastState);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
