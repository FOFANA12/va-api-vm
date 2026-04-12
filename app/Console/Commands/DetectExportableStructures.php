<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Structure;

class DetectExportableStructures extends Command
{
    protected $signature = 'structure:detect-exportable';
    protected $description = 'Detect structures eligible for Action Plan export';

    public function handle()
    {
        $this->info('🔍 Détection des structures éligibles...');

        $structures = Structure::with(['children.actionPlans.actions'])->get();

        $eligibleStructures = [];

        foreach ($structures as $structure) {

            $hasValidChild = $structure->children->contains(function ($child) {

                $actionPlan = $child->actionPlans->where('status', true)->first();

                return $actionPlan && $actionPlan->actions->count() > 0;
            });

            if ($hasValidChild) {
                $eligibleStructures[] = $structure;
            }
        }

        if (empty($eligibleStructures)) {
            $this->warn('❌ Aucune structure éligible trouvée.');
            return;
        }

        $this->info("✅ " . count($eligibleStructures) . " structure(s) trouvée(s) :");

        foreach ($eligibleStructures as $structure) {
            $this->line("- {$structure->name} ({$structure->abbreviation})");
        }

        return Command::SUCCESS;
    }
}
