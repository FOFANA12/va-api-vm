<?php

namespace App\Repositories;

use App\Http\Resources\ActionStatusResource;
use App\Models\Action;
use App\Models\ActionStatus as ModelActionStatus;
use App\Support\ActionStatus;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActionStatusRepository
{
    /**
     *  List all statuses for a given action.
     */
    public function index($actionId)
    {
        $query = ModelActionStatus::where('action_id', $actionId)
            ->orderByDesc('created_at');

        return ActionStatusResource::collection($query->get());
    }

    /**
     * Retrieve available action statuses with localized labels.
     */
    public function requirements(Action $action)
    {
        $current = $action->status;
        $next = ActionStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = ActionStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new actioon status.
     */
    public function store(Request $request, Action $action)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');
            $this->applyStatusEffects($action, $statusCode);

            $status = ModelActionStatus::create([
                'action_uuid' => $action->uuid,
                'action_id' => $action->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $action->status = $status->status_code;
            $action->status_changed_at = $status->status_date;
            $action->status_changed_by = $status->created_by;

            // Pas de mise à jour updated_at
            $action->timestamps = false;
            $action->save();

            DB::commit();

            return new ActionStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete multiple action statuses.
     */
    public function destroy(Request $request, Action $action)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $action->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $action->statuses()->latest('status_date')->first();

            if (!$lastStatus) {
                // Aucun statut restant → reset complet
                $action->status = null;
                $action->actual_start_date = null;
                $action->actual_end_date = null;
                $action->status_changed_at = null;
                $action->status_changed_by = null;
            } else {
                $statusCode = $lastStatus->status_code;
                $this->applyStatusEffects($action, $statusCode, $lastStatus->status_date);

                $action->status = $statusCode;
                $action->status_changed_at = $lastStatus->status_date;
                $action->status_changed_by = $lastStatus->updated_by ?? $lastStatus->created_by;
            }

            $action->timestamps = false;
            $action->save();

            DB::commit();

            return new ActionStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Apply business rules for real dates based on status.
     */
    private function applyStatusEffects(Action $action, string $statusCode, ?string $statusDate = null): void
    {
        $date = $statusDate ?? now();

        switch ($statusCode) {

            case 'draft':
            case 'created':
            case 'planned':
                // Retour arrière → nettoyage complet
                $action->actual_start_date = null;
                $action->actual_end_date = null;
                break;

            case 'in_progress':
                // En réalisation → must have start date
                if (empty($action->actual_start_date)) {
                    $action->actual_start_date = $date;
                }
                $action->actual_end_date = null;
                break;

            case 'stopped':
                // En arrêt → pas de fin réelle
                $action->actual_end_date = null;
                break;

            case 'closed':
                // Clôturé → end date = date du statut
                $action->actual_end_date = $date;
                break;
        }
    }
}
