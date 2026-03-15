<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStructureUniqueIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structures:fix-unique';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix unique constraints on structures table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing structures unique constraints...');

        DB::statement('ALTER TABLE structures DROP INDEX structures_abbreviation_unique');
        DB::statement('ALTER TABLE structures DROP INDEX structures_name_unique');

        DB::statement('ALTER TABLE structures ADD UNIQUE unique_parent_abbreviation (parent_uuid, abbreviation)');
        DB::statement('ALTER TABLE structures ADD UNIQUE unique_parent_name (parent_uuid, name)');

        $this->info('Done.');
    }
}
