<?php

namespace Tests\Feature;

use App\Http\Requests\StrategicObjectiveRequest;
use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Models\User;
use App\Repositories\StrategicObjectiveRepository;
use App\Support\StrategicObjectiveStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StrategicObjectiveStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_objective_creation_records_declared_initial_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$structure, $map, $element] = $this->createObjectiveDependencies($user);

        $request = StrategicObjectiveRequest::create('/api/strategic-objectives', 'POST', [
            'structure' => $structure->uuid,
            'strategic_map' => $map->uuid,
            'strategic_element' => $element->uuid,
            'lead_structure' => $structure->uuid,
            'name' => 'Objectif historique initial',
            'description' => 'Description',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'priority' => 'high',
            'risk_level' => 'low',
        ]);

        app(StrategicObjectiveRepository::class)->store($request);

        $objective = StrategicObjective::query()->latest('id')->firstOrFail();

        $this->assertSame(StrategicObjectiveStatus::DECLARED, $objective->status);
        $this->assertNotNull($objective->status_changed_at);
        $this->assertSame($user->uuid, $objective->status_changed_by);
        $this->assertDatabaseHas('strategic_objective_statuses', [
            'strategic_objective_uuid' => $objective->uuid,
            'strategic_objective_id' => $objective->id,
            'status_code' => StrategicObjectiveStatus::DECLARED,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);
    }

    private function createObjectiveDependencies(User $user): array
    {
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

        return [$structure, $map, $element];
    }
}
