<?php

namespace App\Repositories;

use App\Http\Requests\ActionPlanningRequest;
use App\Http\Resources\ActionPlanningResource;
use App\Jobs\EvaluateActionJob;
use App\Models\Action;
use App\Models\ActionPeriod;
use App\Support\FrequencyUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActionPlanningRepository
{
    /**
     * Load requirements data
     */
    public function requirements()
    {
        $step = 5;
        $periodProgressValues = [];

        for ($i = 0; $i <= 100; $i += $step) {
            $periodProgressValues[] = $i;
        }

        $frequencyUnits =  collect(FrequencyUnit::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        return [
            'frequency_units' => $frequencyUnits,
            'period_progress_values' => $periodProgressValues,
        ];
    }

    /**
     * Show a specific action (planning).
     */
    public function show(Action $action)
    {
        return ['action_planning' => new ActionPlanningResource($action->load('periods'))];
    }

    /**
     * Update action planning.
     */
    public function update(ActionPlanningRequest $request, Action $action)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'is_planned' => true,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $action->fill($request->only([
                'start_date',
                'end_date',
                'is_planned',
                'frequency_unit',
                'frequency_value',
                'updated_by'
            ]));

            $action->save();

            $existingPeriodIds = $action->periods()->pluck('id')->toArray();
            $requestPeriodIds = collect($request->input('periods', []))
                ->pluck('id')
                ->filter()
                ->toArray();

            $periodsToDelete = array_diff($existingPeriodIds, $requestPeriodIds);
            ActionPeriod::whereIn('id', $periodsToDelete)
                ->where('action_uuid', $action->uuid)
                ->delete();

            foreach ($request->input('periods', []) as $period) {
                ActionPeriod::updateOrCreate(
                    [
                        'id' => $period['id'] ?? null,
                        'action_uuid' => $action->uuid,
                    ],
                    [
                        'start_date' => $period['start_date'],
                        'end_date' => $period['end_date'],
                        'progress_percent' => $period['progress_percent'],
                    ]
                );
            }

            $action->load('periods');

            DB::commit();

            dispatch(new EvaluateActionJob($action->uuid));

             return (new ActionPlanningResource($action))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
