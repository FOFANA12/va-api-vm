<?php

namespace Tests\Feature;

use App\Http\Requests\IndicatorPlanningRequest;
use App\Http\Requests\IndicatorRequest;
use App\Exceptions\DomainException;
use App\Models\Indicator;
use App\Models\IndicatorCategory;
use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Models\User;
use App\Repositories\IndicatorPlanningRepository;
use App\Repositories\IndicatorRepository;
use App\Repositories\IndicatorStatusRepository;
use App\Repositories\StrategicObjectiveStatusRepository;
use App\Support\IndicatorStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndicatorStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_indicator_creation_records_created_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $indicator = $this->storeIndicator($user, [
            'name' => 'Indicateur creation',
        ]);

        $this->assertSame(IndicatorStatus::CREATED, $indicator->fresh()->status);
        $this->assertDatabaseHas('indicator_statuses', [
            'indicator_uuid' => $indicator->uuid,
            'indicator_id' => $indicator->id,
            'status_code' => IndicatorStatus::CREATED,
        ]);
    }

    public function test_indicator_validation_accepts_exact_objective_date_bounds(): void
    {
        $user = User::factory()->create();
        [$structure, $map, $element, $objective, $category] = $this->createIndicatorDependencies($user);

        $request = IndicatorRequest::create('/api/indicators', 'POST', [
            'structure' => $structure->uuid,
            'strategic_map' => $map->uuid,
            'strategic_element' => $element->uuid,
            'strategic_objective' => $objective->uuid,
            'category' => $category->uuid,
            'name' => 'Indicateur bornes exactes',
            'description' => 'Description',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'chart_type' => 'LINE',
            'initial_value' => 0,
            'final_target_value' => 100,
            'unit' => '%',
        ]);

        $validator = Validator::make(
            $request->all(),
            $request->rules(),
            [],
            $request->attributes()
        );
        $request->withValidator($validator);

        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    public function test_indicator_creation_is_rejected_when_objective_status_is_frozen(): void
    {
        $user = User::factory()->create();
        [$structure, $map, $element, $objective, $category] = $this->createIndicatorDependencies($user);
        $objective->update(['status' => 'closed']);

        $request = IndicatorRequest::create('/api/indicators', 'POST', [
            'structure' => $structure->uuid,
            'strategic_map' => $map->uuid,
            'strategic_element' => $element->uuid,
            'strategic_objective' => $objective->uuid,
            'category' => $category->uuid,
            'name' => 'Indicateur objectif fige',
            'description' => 'Description',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'chart_type' => 'LINE',
            'initial_value' => 0,
            'final_target_value' => 100,
            'unit' => '%',
        ]);

        $validator = Validator::make(
            $request->all(),
            $request->rules(),
            [],
            $request->attributes()
        );
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('strategic_objective'));
    }

    public function test_manual_status_requirements_do_not_expose_planned(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $indicator = $this->storeIndicator($user, [
            'name' => 'Indicateur requirements',
        ]);

        $requirements = app(IndicatorStatusRepository::class)->requirements($indicator->fresh());
        $codes = collect($requirements['statuses'])->pluck('code')->all();

        $this->assertSame([], $codes);
        $this->assertNotContains(IndicatorStatus::PLANNED, $codes);
    }

    public function test_planning_sets_planned_status_automatically_once(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $indicator = $this->storeIndicator($user, [
            'name' => 'Indicateur planning',
        ]);

        $request = IndicatorPlanningRequest::create('/api/indicator-plannings/' . $indicator->uuid, 'PUT', [
            'frequency_unit' => 'months',
            'frequency_value' => 1,
            'periods' => [
                [
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-06-30',
                    'target_value' => 50,
                ],
                [
                    'start_date' => '2026-07-01',
                    'end_date' => '2026-12-31',
                    'target_value' => 100,
                ],
            ],
        ]);

        app(IndicatorPlanningRepository::class)->update($request, $indicator->fresh());
        app(IndicatorPlanningRepository::class)->update($request, $indicator->fresh());

        $indicator->refresh();

        $this->assertTrue($indicator->is_planned);
        $this->assertSame(IndicatorStatus::PLANNED, $indicator->status);
        $this->assertSame(1, $indicator->statuses()->where('status_code', IndicatorStatus::PLANNED)->count());
    }

    public function test_declared_objective_does_not_allow_indicator_to_start_progress(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $indicator = $this->storeIndicator($user, [
            'name' => 'Indicateur objectif declare',
        ]);
        $indicator->strategicObjective()->update(['status' => 'declared']);

        app(IndicatorPlanningRepository::class)->update($this->planningRequest($indicator), $indicator->fresh());

        $requirements = app(IndicatorStatusRepository::class)->requirements($indicator->fresh());
        $codes = collect($requirements['statuses'])->pluck('code')->all();

        $this->assertSame([], $codes);

        $this->expectException(DomainException::class);

        app(IndicatorStatusRepository::class)->store(
            \Illuminate\Http\Request::create('/api/indicator-statuses/' . $indicator->uuid, 'POST', [
                'status' => IndicatorStatus::IN_PROGRESS,
            ]),
            $indicator->fresh()
        );
    }

    public function test_engaged_objective_allows_indicator_workflow(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $indicator = $this->storeIndicator($user, [
            'name' => 'Indicateur objectif engage',
        ]);

        app(IndicatorPlanningRepository::class)->update($this->planningRequest($indicator), $indicator->fresh());

        $requirements = app(IndicatorStatusRepository::class)->requirements($indicator->fresh());
        $codes = collect($requirements['statuses'])->pluck('code')->all();

        $this->assertSame([IndicatorStatus::IN_PROGRESS], $codes);
    }

    public function test_closed_objective_freezes_indicator_status_and_planning(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $indicator = $this->storeIndicator($user, [
            'name' => 'Indicateur objectif cloture',
        ]);
        $indicator->strategicObjective()->update(['status' => 'closed']);

        $requirements = app(IndicatorStatusRepository::class)->requirements($indicator->fresh());
        $codes = collect($requirements['statuses'])->pluck('code')->all();

        $this->assertSame([], $codes);

        $this->expectException(DomainException::class);

        app(IndicatorPlanningRepository::class)->update($this->planningRequest($indicator), $indicator->fresh());
    }

    public function test_closing_objective_propagates_indicator_status_with_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $indicator = $this->storeIndicator($user, [
            'name' => 'Indicateur propagation',
        ]);

        $request = \Illuminate\Http\Request::create('/api/strategic-objective-statuses', 'POST', [
            'status' => 'closed',
        ]);

        app(StrategicObjectiveStatusRepository::class)->store($request, $indicator->strategicObjective);

        $indicator->refresh();

        $this->assertSame(IndicatorStatus::CLOSED, $indicator->status);
        $this->assertNotNull($indicator->actual_end_date);
        $this->assertDatabaseHas('indicator_statuses', [
            'indicator_uuid' => $indicator->uuid,
            'indicator_id' => $indicator->id,
            'status_code' => IndicatorStatus::CLOSED,
        ]);
    }

    private function storeIndicator(User $user, array $overrides = []): Indicator
    {
        [$structure, $map, $element, $objective, $category] = $this->createIndicatorDependencies($user);

        $request = IndicatorRequest::create('/api/indicators', 'POST', array_merge([
            'structure' => $structure->uuid,
            'strategic_map' => $map->uuid,
            'strategic_element' => $element->uuid,
            'strategic_objective' => $objective->uuid,
            'category' => $category->uuid,
            'name' => 'Indicateur test',
            'description' => 'Description',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'chart_type' => 'LINE',
            'initial_value' => 0,
            'final_target_value' => 100,
            'unit' => '%',
        ], $overrides));

        app(IndicatorRepository::class)->store($request);

        return Indicator::query()->latest('id')->firstOrFail();
    }

    private function planningRequest(Indicator $indicator): IndicatorPlanningRequest
    {
        return IndicatorPlanningRequest::create('/api/indicator-plannings/' . $indicator->uuid, 'PUT', [
            'frequency_unit' => 'months',
            'frequency_value' => 1,
            'periods' => [
                [
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-06-30',
                    'target_value' => 50,
                ],
                [
                    'start_date' => '2026-07-01',
                    'end_date' => '2026-12-31',
                    'target_value' => 100,
                ],
            ],
        ]);
    }

    private function createIndicatorDependencies(User $user): array
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

        $objective = StrategicObjective::create([
            'uuid' => (string) Str::uuid(),
            'reference' => 'OBJ-' . Str::upper(Str::random(6)),
            'name' => 'Objectif test ' . Str::random(4),
            'structure_uuid' => $structure->uuid,
            'strategic_map_uuid' => $map->uuid,
            'strategic_element_uuid' => $element->uuid,
            'lead_structure_uuid' => $structure->uuid,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
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

        return [$structure, $map, $element, $objective, $category];
    }
}
