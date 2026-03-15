<?php

namespace App\Console\Commands;

use App\Helpers\ReferenceGenerator;
use Illuminate\Console\Command;
use App\Models\StrategicElement;

class FixStrategicElementReferences extends Command
{
    protected $signature = 'references:fix-strategic-elements';

    protected $description = 'Fix or generate references for existing strategic elements';

    public function handle()
    {
        $this->info('Fixing strategic element references...');

        $elements = StrategicElement::with(['structure', 'strategicMap'])->get();

        $count = 0;

        foreach ($elements as $element) {

            if (!$element->structure || !$element->strategicMap) {
                $this->warn("Skipping element ID {$element->id} (missing relations)");
                continue;
            }

            $reference = ReferenceGenerator::generateStrategicElementReference(
                $element->id,
                $element->type,
                $element->structure->abbreviation
            );

            $element->update([
                'reference' => $reference
            ]);

            $count++;
        }

        $this->info("Updated {$count} strategic element references.");

        return Command::SUCCESS;
    }
}
