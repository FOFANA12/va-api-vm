<?php

namespace App\Console\Commands;

use App\Jobs\EvaluateActionJob;
use App\Jobs\EvaluateStrategicObjectiveJob;
use App\Jobs\UpdateActionAlignmentMetricsJob;
use App\Jobs\UpdateActionProgressJob;
use App\Jobs\UpdateActionTotalsJob;
use App\Jobs\UpdateStructureAlignmentMetricsJob;
use App\Models\Action;
use App\Models\StrategicObjective;
use App\Models\Structure;
use Illuminate\Console\Command;

class RunBackgroundCalculations extends Command
{
    protected $signature = 'maintenance:run-background-calculations
                            {--chunk=200 : Number of records loaded per batch}';

    protected $description = 'Run background calculation and evaluation jobs directly without a queue worker';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');

        if ($chunk < 1) {
            $this->error('Option --chunk must be greater than zero.');
            return self::FAILURE;
        }

        $actionCount = $this->refreshActions($chunk);
        $structureCount = $this->refreshStructures($chunk);
        $objectiveCount = $this->refreshObjectives($chunk);

        $this->info(
            "Calculations refreshed: {$actionCount} action(s), {$structureCount} structure(s), {$objectiveCount} strategic objective(s)."
        );

        return self::SUCCESS;
    }

    private function refreshActions(int $chunk): int
    {
        $count = 0;

        Action::query()
            ->select('id', 'uuid')
            ->chunkById($chunk, function ($actions) use (&$count) {
                foreach ($actions as $action) {
                    (new UpdateActionTotalsJob($action->uuid))->handle();
                    (new UpdateActionProgressJob($action->uuid))->handle();
                    (new UpdateActionAlignmentMetricsJob($action->uuid))->handle();
                    (new EvaluateActionJob($action->uuid))->handle();
                    $count++;
                }
            });

        return $count;
    }

    private function refreshStructures(int $chunk): int
    {
        $count = 0;

        Structure::query()
            ->select('id', 'uuid')
            ->chunkById($chunk, function ($structures) use (&$count) {
                foreach ($structures as $structure) {
                    (new UpdateStructureAlignmentMetricsJob($structure->uuid))->handle();
                    $count++;
                }
            });

        return $count;
    }

    private function refreshObjectives(int $chunk): int
    {
        $count = 0;

        StrategicObjective::query()
            ->select('id', 'uuid')
            ->chunkById($chunk, function ($objectives) use (&$count) {
                foreach ($objectives as $objective) {
                    (new EvaluateStrategicObjectiveJob($objective->uuid))->handle();
                    $count++;
                }
            });

        return $count;
    }
}
