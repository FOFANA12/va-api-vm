<?php

namespace App\Repositories;

use App\Http\Requests\IndicatorPlanningRequest;
use App\Http\Resources\IndicatorPlanningResource;
use App\Jobs\EvaluateStrategicObjectiveJob;
use App\Models\Indicator;
use App\Models\IndicatorPeriod;
use App\Support\FrequencyUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IndicatorPlanningRepository
{
    /**
     * Load requirements data
     */
    public function requirements()
    {
        $frequencyUnits =  collect(FrequencyUnit::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        return [
            'frequency_units' => $frequencyUnits,
        ];
    }

    /**
     * Show a specific indicator (planning).
     */
    public function show(Indicator $indicator)
    {
        return ['indicator_planning' => new IndicatorPlanningResource($indicator->load(['periods', 'strategicObjective']))];
    }

    /**
     * Update indicator planning.
     */
    public function update(IndicatorPlanningRequest $request, Indicator $indicator)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'is_planned' => true,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $indicator->fill($request->only([
                'frequency_unit',
                'frequency_value',
                'is_planned',
                'updated_by'
            ]));

            $indicator->save();

            $existingPeriodIds = $indicator->periods()->pluck('id')->toArray();
            $requestPeriodIds = collect($request->input('periods', []))
                ->pluck('id')
                ->filter()
                ->toArray();

            $periodsToDelete = array_diff($existingPeriodIds, $requestPeriodIds);
            IndicatorPeriod::whereIn('id', $periodsToDelete)
                ->where('indicator_uuid', $indicator->uuid)
                ->delete();

            foreach ($request->input('periods', []) as $period) {
                IndicatorPeriod::updateOrCreate(
                    [
                        'id' => $period['id'] ?? null,
                        'indicator_uuid' => $indicator->uuid,
                    ],
                    [
                        'start_date' => $period['start_date'],
                        'end_date' => $period['end_date'],
                        'target_value' => $period['target_value'],
                    ]
                );
            }

            $indicator->load(['periods', 'strategicObjective']);

            DB::commit();

            dispatch(new EvaluateStrategicObjectiveJob($indicator->strategic_objective_uuid));

            return (new IndicatorPlanningResource($indicator))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
