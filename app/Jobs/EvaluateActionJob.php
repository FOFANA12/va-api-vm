<?php

namespace App\Jobs;

use App\Models\Action;
use App\Models\ActionObjectiveAlignment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EvaluateActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $actionUuid;

    public function __construct(string $actionUuid)
    {
        $this->actionUuid = $actionUuid;
    }

    public function handle(): void
    {
        $today = Carbon::today();
        $next7Days = $today->copy()->addDays(7);

        $action = Action::where('uuid', $this->actionUuid)
            ->with('periods.controls')
            ->first();

        if (!$action) {
            return;
        }

        // Action en vain si aucun alignement objectif
        $hasAlignment = ActionObjectiveAlignment::where('action_uuid', $action->uuid)->exists();
        $failed = !$hasAlignment;

        $alert = false;

        if ($action->periods) {


            if ($action->periods->isNotEmpty()) {
                // Periods ending in ≤ 7 days without any control
                $nearDeadline = $action->periods()
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '<=', $next7Days)
                    ->whereDoesntHave('controls')
                    ->exists();

                //no strict version
                // $nearDeadline = $action->periods()
                //     ->whereDate('end_date', '<=', $next7Days)
                //     ->whereDoesntHave('controls')
                //     ->exists();

                // Periods already overdue without any control
                $overdue = $action->periods()
                    ->whereDate('end_date', '<', $today)
                    ->whereDoesntHave('controls')
                    ->exists();

                // Objective expired but some indicator periods are still not controlled
                $expiredWithMissingControls = $action->end_date && $action->end_date <= $today &&
                    $action->periods()
                    ->whereDoesntHave('controls')
                    ->exists();

                $alert = $nearDeadline || $overdue || $expiredWithMissingControls;
            }
        }

        $action->updateQuietly([
            'failed' => $failed,
            'alert' => $alert,
        ]);
    }
}
