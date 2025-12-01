<?php

namespace App\Repositories\Settings;

use App\Models\DefaultPhase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\DefaultPhaseRequest;
use App\Http\Resources\Settings\DefaultPhaseResource;

class DefaultPhaseRepository
{
    /**
     * List all default phases.
     */
    public function index()
    {
        $phases = DefaultPhase::orderBy('number', 'asc')->get();

        return DefaultPhaseResource::collection($phases);
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
     * Create a new default phase.
     */
    public function store(DefaultPhaseRequest $request)
    {
        $request->merge([
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $defaultPhase = DefaultPhase::create($request->only([
            'name',
            'duration',
            'weight',
            'number',
            'description',
            'deliverable',
            'created_by',
            'updated_by',
        ]));

        return new DefaultPhaseResource($defaultPhase);
    }

    /**
     * Show a specific default phase.
     */
    public function show(DefaultPhase $defaultPhase)
    {
        return [
            'default_phase' => new DefaultPhaseResource($defaultPhase),
        ];
    }

    /**
     * Update an default phase.
     */
    public function update(DefaultPhaseRequest $request, DefaultPhase $defaultPhase)
    {
        $request->merge([
            'updated_by' => Auth::user()?->uuid,
        ]);

        $defaultPhase->fill($request->only([
            'name',
            'duration',
            'weight',
            'number',
            'description',
            'deliverable',
            'updated_by',
        ]));

        $defaultPhase->save();

        return new DefaultPhaseResource($defaultPhase);
    }

    /**
     * Delete a single default phase.
     */
    public function destroy(DefaultPhase $defaultPhase)
    {
        try {
            $defaultPhase->delete();
        } catch (\Throwable $e) {
            if ((string) $e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
