<?php

namespace App\Console\Commands;

use App\Models\Indicator;
use Illuminate\Console\Command;

class SyncIndicatorDatesFromObjectives extends Command
{
    protected $signature = 'indicators:sync-dates-from-objectives';

    protected $description = 'Copy indicator dates from their linked strategic objectives';

    public function handle()
    {
        $this->info('Syncing indicator dates from strategic objectives...');

        $updated = 0;
        $skipped = 0;

        Indicator::with('strategicObjective')
            ->chunkById(200, function ($indicators) use (&$updated, &$skipped) {
                foreach ($indicators as $indicator) {
                    $objective = $indicator->strategicObjective;

                    if (!$objective) {
                        $skipped++;
                        $this->warn("Skipping indicator ID {$indicator->id} (missing strategic objective)");
                        continue;
                    }

                    $indicator->update([
                        'start_date' => $objective->start_date,
                        'end_date' => $objective->end_date,
                    ]);

                    $updated++;
                }
            });

        $this->info("Updated {$updated} indicators.");

        if ($skipped > 0) {
            $this->warn("Skipped {$skipped} indicators.");
        }

        return Command::SUCCESS;
    }
}
