<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Structure;

class DetectBilanExportableStructures extends Command
{
    protected $signature = 'structure:detect-bilan-exportable';
    protected $description = 'Detect structures eligible for Bilan export';

    public function handle()
    {
        $this->info('🔍 Détection des structures éligibles pour le Bilan consolidé...');

        $structures = Structure::with([
            'children.strategicMaps.objectives',
            'children.actionPlans.actions'
        ])->get();

        $eligibleStructures = [];

        foreach ($structures as $structure) {

            $hasValidChild = $structure->children->contains(function ($child) {

                // ✅ Plan d'action actif avec actions
                $actionPlan = $child->actionPlans->where('status', true)->first();

                $hasOperational = $actionPlan
                    && $actionPlan->actions->count() > 0;

                // ✅ Carte stratégique active
                $strategicMap = $child->strategicMaps->where('status', true)->first();

                $hasStrategic = $strategicMap !== null;

                // ✅ CONDITION FINALE (AND)
                return $hasOperational && $hasStrategic;
            });

            if ($hasValidChild) {
                $eligibleStructures[] = $structure;
            }
        }

        if (empty($eligibleStructures)) {
            $this->warn('❌ Aucune structure éligible trouvée.');
            return Command::SUCCESS;
        }

        $this->info("✅ " . count($eligibleStructures) . " structure(s) trouvée(s) :");

        foreach ($eligibleStructures as $structure) {
            $this->line("- {$structure->name} ({$structure->abbreviation})");
        }

        return Command::SUCCESS;
    }
}
