<?php

namespace Tests\Feature;

use App\Models\Indicator;
use App\Models\IndicatorCategory;
use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class SyncIndicatorDatesFromObjectivesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_indicator_dates_from_objective(): void
    {
        $user = User::factory()->create();

        $structure = Structure::create([
            'uuid' => (string) Str::uuid(),
            'abbreviation' => 'STR',
            'name' => 'Structure test',
            'type' => 'STATE',
            'status' => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $map = StrategicMap::create([
            'uuid' => (string) Str::uuid(),
            'structure_uuid' => $structure->uuid,
            'name' => 'Carte test',
            'description' => 'Description',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $element = StrategicElement::create([
            'uuid' => (string) Str::uuid(),
            'reference' => 'EL-' . Str::upper(Str::random(6)),
            'structure_uuid' => $structure->uuid,
            'strategic_map_uuid' => $map->uuid,
            'type' => 'LEVER',
            'order' => 1,
            'name' => 'Element test ' . Str::random(4),
            'abbreviation' => 'ET' . Str::upper(Str::random(3)),
            'description' => 'Description',
            'status' => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $objective = StrategicObjective::create([
            'uuid' => (string) Str::uuid(),
            'reference' => 'OBJ-' . Str::upper(Str::random(6)),
            'name' => 'Objectif test ' . Str::random(4),
            'structure_uuid' => $structure->uuid,
            'strategic_map_uuid' => $map->uuid,
            'strategic_element_uuid' => $element->uuid,
            'lead_structure_uuid' => $structure->uuid,
            'start_date' => '2026-02-01',
            'end_date' => '2026-11-30',
            'description' => 'Description',
            'priority' => 'high',
            'risk_level' => 'low',
            'status' => 'engaged',
            'state' => 'none',
            'status_changed_by' => $user->uuid,
            'status_changed_at' => now(),
            'failed' => false,
            'alert' => false,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $category = IndicatorCategory::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Categorie ' . Str::random(6),
            'status' => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $indicator = Indicator::create([
            'uuid' => (string) Str::uuid(),
            'reference' => 'IND-' . Str::upper(Str::random(6)),
            'name' => 'Indicateur test',
            'description' => 'Description',
            'structure_uuid' => $structure->uuid,
            'strategic_map_uuid' => $map->uuid,
            'strategic_element_uuid' => $element->uuid,
            'strategic_objective_uuid' => $objective->uuid,
            'lead_structure_uuid' => $structure->uuid,
            'category_uuid' => $category->uuid,
            'chart_type' => 'LINE',
            'initial_value' => 0,
            'final_target_value' => 100,
            'achieved_value' => 0,
            'unit' => '%',
            'status' => 'created',
            'state' => 'none',
            'is_planned' => false,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        Artisan::call('indicators:sync-dates-from-objectives');

        $indicator->refresh();

        $this->assertSame('2026-02-01', $indicator->start_date?->toDateString());
        $this->assertSame('2026-11-30', $indicator->end_date?->toDateString());
    }
}
