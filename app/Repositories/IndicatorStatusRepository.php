<?php

namespace App\Repositories;

use App\Http\Resources\IndicatorStatusResource;
use App\Models\Indicator;
use App\Models\IndicatorStatus as ModelsIndicatorStatus;
use App\Support\IndicatorStatus;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IndicatorStatusRepository
{
    /**
     *  List all statuses for a given indicator.
     */
    public function index($indicatorId)
    {
        $query = ModelsIndicatorStatus::where('indicator_id', $indicatorId)
            ->orderByDesc('created_at');

        return IndicatorStatusResource::collection($query->get());
    }

    /**
     * Retrieve available indicator statuses with localized labels.
     */
    public function requirements(Indicator $indicator)
    {
        $current = $indicator->status;
        $next = IndicatorStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = IndicatorStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new indicator status.
     */
    public function store(Request $request, Indicator $indicator)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');
            $this->applyStatusEffects($indicator, $statusCode);

            $status = ModelsIndicatorStatus::create([
                'indicator_uuid' => $indicator->uuid,
                'indicator_id' => $indicator->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $indicator->status = $status->status_code;
            $indicator->status_changed_at = $status->status_date;
            $indicator->status_changed_by = $status->created_by;

            // Pas de mise à jour updated_at
            $indicator->timestamps = false;
            $indicator->save();

            DB::commit();

            return new IndicatorStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete multiple indicator statuses.
     */
    public function destroy(Request $request, Indicator $indicator)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $indicator->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $indicator->statuses()->latest('status_date')->first();

            if (!$lastStatus) {
                // Aucun statut restant → reset complet
                $indicator->status = null;
                $indicator->actual_start_date = null;
                $indicator->actual_end_date = null;
                $indicator->status_changed_at = null;
                $indicator->status_changed_by = null;
            } else {
                $statusCode = $lastStatus->status_code;
                $this->applyStatusEffects($indicator, $statusCode, $lastStatus->status_date);

                $indicator->status = $statusCode;
                $indicator->status_changed_at = $lastStatus->status_date;
                $indicator->status_changed_by = $lastStatus->updated_by ?? $lastStatus->created_by;
            }

            $indicator->timestamps = false;
            $indicator->save();

            DB::commit();

            return new IndicatorStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Apply business rules for real dates based on status.
     */
    private function applyStatusEffects(Indicator $indicator, string $statusCode, ?string $statusDate = null): void
    {
        $date = $statusDate ?? now();

        switch ($statusCode) {

            case 'created':
            case 'planned':
                // Retour arrière → nettoyage complet
                $indicator->actual_start_date = null;
                $indicator->actual_end_date = null;
                break;

            case 'in_progress':
                // En réalisation → must have start date
                if (empty($indicator->actual_start_date)) {
                    $indicator->actual_start_date = $date;
                }
                $indicator->actual_end_date = null;
                break;

            case 'stopped':
                // En arrêt → pas de fin réelle
                $indicator->actual_end_date = null;
                break;

            case 'closed':
                // Clôturé → end date = date du statut
                $indicator->actual_end_date = $date;
                break;
        }
    }
}
