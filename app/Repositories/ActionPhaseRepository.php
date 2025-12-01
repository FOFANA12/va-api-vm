<?php

namespace App\Repositories;

use App\Http\Requests\ActionPhaseRequest;
use App\Http\Resources\ActionPhaseResource;
use App\Models\Action;
use App\Models\ActionPhase;
use App\Models\DefaultPhase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActionPhaseRepository
{
    /**
     * List all phases of the given action.
     */
    public function index(Action $action)
    {
        $action->load([
            'phases' => function ($q) {
                $q->orderBy('number', 'asc')
                    ->with([
                        'tasks' => function ($t) {
                            $t->orderBy('start_date', 'asc')
                                ->with('assignedTo');
                        },
                    ]);
            },
        ]);

        return ActionPhaseResource::collection($action->phases);
    }

    /**
     * Load requirements data
     */
    public function requirements()
    {
        $coefficientValues = [
            0.05,
            0.10,
            0.15,
            0.20,
            0.25,
            0.30,
            0.35,
            0.40,
            0.45,
            0.50,
            0.55,
            0.60,
            0.65,
            0.70,
            0.75,
            0.80,
            0.85,
            0.90,
            0.95,
            1.00
        ];

        $formattedValues = [];

        foreach ($coefficientValues as $value) {
            $formattedValues[] = [
                'value' => $value,
                'label' => number_format($value, 2),
            ];
        }

        return [
            'weight_coefficients' => $formattedValues,
        ];
    }

    /**
     * Create a new action phase.
     */
    public function store(ActionPhaseRequest $request, Action $action)
    {
        $request->merge([
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $actionPhase = $action->phases()->create($request->only([
            'name',
            'description',
            'deliverable',
            'start_date',
            'end_date',
            'weight',
            'number',
            'created_by',
            'updated_by',
        ]));

        return new ActionPhaseResource($actionPhase);
    }

    /**
     * Show a specific action phase.
     */
    public function show(ActionPhase $actionPhase)
    {
        $actionPhase->load('tasks.assignedTo');

        return [
            'action_phase' => new ActionPhaseResource($actionPhase),
        ];
    }

    /**
     * Update an action phase.
     */
    public function update(ActionPhaseRequest $request, ActionPhase $actionPhase)
    {
        $request->merge([
            'updated_by' => Auth::user()?->uuid,
        ]);

        $actionPhase->fill($request->only([
            'name',
            'description',
            'deliverable',
            'start_date',
            'end_date',
            'weight',
            'number',
            'updated_by',
        ]));

        $actionPhase->save();

        return new ActionPhaseResource($actionPhase);
    }

    /**
     * Initialize default phases for a given action.
     */
    public function initializeDefaultPhases(Action $action)
    {
        if (empty($action->start_date) || empty($action->end_date)) {
            return;
        }

        $defaultPhases = DefaultPhase::orderBy('number')->get();
        if ($defaultPhases->isEmpty()) {
            return;
        }

        $sumExisting = (float) ActionPhase::where('action_uuid', $action->uuid)->sum('weight');
        $currentWeight = $sumExisting;

        $actionStart = Carbon::parse($action->start_date);
        $actionEnd = Carbon::parse($action->end_date);
        $currentDate = clone $actionStart;

        foreach ($defaultPhases as $phase) {
            $exists = ActionPhase::where('action_uuid', $action->uuid)
                ->where(function ($q) use ($phase) {
                    $q->where('name', $phase->name)
                        ->orWhere('number', $phase->number);
                })
                ->exists();

            if ($exists) continue;

            $phaseWeight = (float) $phase->weight;

            if ($currentWeight + $phaseWeight > 1 + 1e-8) {
                break;
            }

            $phaseStart = clone $currentDate;
            $duration = max(1, (int) $phase->duration);
            $phaseEnd = (clone $phaseStart)->addDays($duration - 1);

            if ($phaseEnd->gt($actionEnd)) {
                $phaseEnd = clone $actionEnd;
            }

            if ($phaseStart->gt($actionEnd)) {
                break;
            }

            ActionPhase::create([
                'action_uuid' => $action->uuid,
                'name' => $phase->name,
                'number' => $phase->number,
                'start_date' => $phaseStart->toDateString(),
                'end_date' => $phaseEnd->toDateString(),
                'weight' => $phaseWeight,
                'description' => $phase->description,
                'deliverable' => $phase->deliverable,
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $currentWeight += $phaseWeight;
            $currentDate = (clone $phaseEnd)->addDay();

            if ($currentDate->gt($actionEnd)) {
                break;
            }
        }
    }

    /**
     * Delete a single action phase.
     */
    public function destroy(ActionPhase $actionPhase)
    {
        try {
            $actionPhase->delete();
        } catch (\Throwable $e) {
            if ((string) $e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
