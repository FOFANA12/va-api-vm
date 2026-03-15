<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StrategicObjective;
use App\Models\StrategicObjectiveStatus;
use Illuminate\Support\Facades\DB;

class InitStrategicObjectiveStatuses extends Command
{
    protected $signature = 'strategic-objectives:init-statuses';

    protected $description = 'Initialize statuses for existing strategic objectives';

    public function handle()
    {
        DB::beginTransaction();

        try {

            $objectives = StrategicObjective::all();

            foreach ($objectives as $objective) {

                StrategicObjectiveStatus::create([
                    'strategic_objective_uuid' => $objective->uuid,
                    'strategic_objective_id' => $objective->id,
                    'status_code' => $objective->status ?? 'draft',
                    'status_date' => now(),
                    'created_by' => $objective->created_by,
                    'updated_by' => $objective->updated_by,
                ]);

            }

            DB::commit();

            $this->info('Strategic objective statuses initialized.');

        } catch (\Throwable $e) {

            DB::rollBack();
            $this->error($e->getMessage());

        }
    }
}