<?php

namespace App\Repositories;

use App\Http\Requests\IndicatorPlanningRequest;
use App\Http\Resources\IndicatorPlanningResource;
use App\Jobs\EvaluateStrategicObjectiveJob;
use App\Models\Indicator;
use App\Models\IndicatorPeriod;
use App\Support\FrequencyUnit;
use App\Support\IndicatorStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\IndicatorStatus as ModelsIndicatorStatus;
use App\Exceptions\DomainException;

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
        return ['indicator_planning' => new IndicatorPlanningResource($indicator->load('periods'))];
    }

    /**
     * Update indicator planning.
     */
    public function update(IndicatorPlanningRequest $request, Indicator $indicator)
    {
        DB::beginTransaction();
        try {
            if (!IndicatorStatus::isAllowedForObjectiveStatus(
                IndicatorStatus::PLANNED,
                $indicator->strategicObjective?->status
            )) {
                throw new DomainException(__('app/indicator.document_not_editable_status'));
            }

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

            if ($indicator->status !== IndicatorStatus::PLANNED) {
                $status = ModelsIndicatorStatus::create([
                    'indicator_uuid' => $indicator->uuid,
                    'indicator_id' => $indicator->id,
                    'status_code' => IndicatorStatus::PLANNED,
                    'status_date' => now(),
                    'created_by' => Auth::user()?->uuid,
                    'updated_by' => Auth::user()?->uuid,
                ]);

                $indicator->update([
                    'status' => $status->status_code,
                    'status_changed_at' => $status->status_date,
                    'status_changed_by' => $status->created_by,
                ]);
            }

            $indicator->refresh()->load('periods');

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
