<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Http\Requests\MatrixPeriodRequest;
use App\Http\Resources\MatrixPeriodResource;
use App\Models\MatrixPeriod;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Support\StrategicObjectiveStatus;
use App\Support\StrategicState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MatrixPeriodRepository
{
    /**
     * Get matrix (periods + strategic objectives) for a specific strategic map.
     */
    public function getMatrixPeriods(StrategicMap $strategicMap)
    {
        $periods = $strategicMap->matrixPeriods()
            ->with('strategicObjectives')
            ->orderBy('start_date')
            ->orderBy('end_date')
            ->get();

        return [
            'matrix_periods' => MatrixPeriodResource::collection($periods),
        ];
    }

    /**
     *  Get available StrategicObjectives
     */
    public function availableObjectives(StrategicMap $strategicMap, ?MatrixPeriod $matrixPeriod = null)
    {
        $strategicElements = $strategicMap->elements()
            ->with(['objectives' => function ($q) use ($matrixPeriod) {
                $q->select([
                    'uuid',
                    'reference',
                    'name',
                    'status',
                    'state',
                    'strategic_axis_uuid',
                    'matrix_period_uuid',
                    'start_date',
                    'end_date',
                ])->orderBy('reference', 'asc');

                if ($matrixPeriod) {
                    $q->where(function ($query) use ($matrixPeriod) {
                        $query->whereNull('matrix_period_uuid')
                            ->where(function ($sub) use ($matrixPeriod) {
                                $sub->whereBetween('start_date', [$matrixPeriod->start_date, $matrixPeriod->end_date])
                                    ->orWhereBetween('end_date', [$matrixPeriod->start_date, $matrixPeriod->end_date]);
                            });
                    });
                } else {
                    $q->whereNull('matrix_period_uuid');
                }
            }])
            ->orderBy('order', 'asc')
            ->get(['uuid', 'name', 'order']);

        $formattedAxes = $strategicElements->map(function ($elt) {
            return [
                'uuid' => $elt->uuid,
                'name' => $elt->name,
                'order' => $elt->order,
                'objectives' => $elt->objectives->map(function ($objective) {
                    return [
                        'uuid' => $objective->uuid,
                        'reference' => $objective->reference,
                        'name' => $objective->name,
                        'start_date' =>  DateTimeFormatter::formatDate($objective->start_date),
                        'end_date' =>  DateTimeFormatter::formatDate($objective->end_date),
                        'status' => StrategicObjectiveStatus::get($objective->status, app()->getLocale()),
                        'state' => StrategicState::get($objective->state, app()->getLocale()),
                    ];
                }),
            ];
        });

        return [
            'strategic_axes' => $formattedAxes,
        ];
    }

    /**
     * Store a new matrix period.
     */
    public function store(MatrixPeriodRequest $request, StrategicMap $strategicMap)
    {
        DB::beginTransaction();
        try {
            $userUuid = Auth::user()?->uuid;

            $matrixPeriod = $strategicMap->matrixPeriods()->create([
                'start_date' => $request->input('start_date'),
                'end_date'   => $request->input('end_date'),
                'created_by' => $userUuid,
                'updated_by' => $userUuid,
            ]);

            DB::commit();

            return new MatrixPeriodResource($matrixPeriod);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific matrix period.
     */
    public function show(MatrixPeriod $matrixPeriod)
    {
        return ['matrix_period' => new MatrixPeriodResource($matrixPeriod->load([
            'strategicObjectives',
        ]))];
    }

    /**
     * Update an matrix period.
     */
    public function update(MatrixPeriodRequest $request, MatrixPeriod $matrixPeriod)
    {
        DB::beginTransaction();
        try {
            $userUuid = Auth::user()?->uuid;

            $matrixPeriod->fill([
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'updated_by' => $userUuid,
            ])->save();

            DB::commit();

            return new MatrixPeriodResource($matrixPeriod);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a single matrix period.
     */
    public function destroy(MatrixPeriod $matrixPeriod)
    {
        try {
            $matrixPeriod->delete();
        } catch (\Throwable $e) {
            if ((string) $e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }


    /**
     * Attach StrategicObjectives to a given MatrixPeriod.
     *
     */
    public function attachObjectives(MatrixPeriod $matrixPeriod, Request $request)
    {
        DB::beginTransaction();

        try {
            $objectiveUuids = $request->input('objectives', []);

            if (!empty($objectiveUuids)) {
                StrategicObjective::whereIn('uuid', $objectiveUuids)
                    ->update(['matrix_period_uuid' => $matrixPeriod->uuid]);
            }

            $matrixPeriod->load('strategicObjectives');

            DB::commit();

            return new MatrixPeriodResource($matrixPeriod);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Detach a single StrategicObjective from a MatrixPeriod.
     */
    public function detachObjective(MatrixPeriod $matrixPeriod, string $objectiveUuid)
    {
        DB::beginTransaction();

        try {
            $objective = StrategicObjective::where('uuid', $objectiveUuid)
                ->where('matrix_period_uuid', $matrixPeriod->uuid)
                ->first();

            if (!$objective) {
                throw new \Exception(__('app/matrix_period.repository.objective_not_found_or_not_attached'));
            }

            $objective->update(['matrix_period_uuid' => null]);

            $matrixPeriod->load('strategicObjectives');

            DB::commit();

            return new MatrixPeriodResource($matrixPeriod);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
