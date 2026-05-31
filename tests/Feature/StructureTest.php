<?php

namespace Tests\Feature;

use App\Models\Structure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_name_allowed_for_different_parent()
    {
        $user = User::factory()->create();

        $parent1 = Structure::create([
            'uuid' => Str::uuid(),
            'name' => 'Ministère Santé',
            'abbreviation' => 'MS',
            'type' => 'STRATEGIC',
            'status' => true
        ]);

        $parent2 = Structure::create([
            'uuid' => Str::uuid(),
            'name' => 'Ministère Education',
            'abbreviation' => 'ME',
            'type' => 'STRATEGIC',
            'status' => true
        ]);

        $this->actingAs($user);

        $data = [
            'name' => 'Direction Informatique',
            'abbreviation' => 'DI',
            'parent' => $parent1->uuid,
            'type' => 'OPERATIONAL'
        ];

        $this->postJson('/api/structures', $data)
            ->assertStatus(201);

        $data['parent'] = $parent2->uuid;

        $this->postJson('/api/structures', $data)
            ->assertStatus(201);
    }

    public function test_same_name_not_allowed_for_same_parent()
    {
        $user = User::factory()->create();

        $parent = Structure::create([
            'uuid' => Str::uuid(),
            'name' => 'Ministère Santé',
            'abbreviation' => 'MS',
            'type' => 'STRATEGIC',
            'status' => true
        ]);

        $this->actingAs($user);

        $data = [
            'name' => 'Direction Informatique',
            'abbreviation' => 'DI',
            'parent' => $parent->uuid,
            'type' => 'OPERATIONAL'
        ];

        $this->postJson('/api/structures', $data)
            ->assertStatus(201);

        $this->postJson('/api/structures', $data)
            ->assertStatus(422);
    }

    public function test_structure_cannot_be_updated_with_itself_as_parent()
    {
        $user = User::factory()->create();
        $structure = $this->createStructure('DPEF', 'Direction Projets', 'STRATEGIC');

        $this->actingAs($user)
            ->putJson("/api/structures/{$structure->id}", [
                'name' => $structure->name,
                'abbreviation' => $structure->abbreviation,
                'parent' => $structure->uuid,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent');
    }

    public function test_structure_cannot_be_updated_with_its_descendant_as_parent()
    {
        $user = User::factory()->create();
        $parent = $this->createStructure('DPEF', 'Direction Projets', 'STRATEGIC');
        $child = $this->createStructure('BID', 'Projet BID', 'OPERATIONAL', $parent->uuid);

        $this->actingAs($user)
            ->putJson("/api/structures/{$parent->id}", [
                'name' => $parent->name,
                'abbreviation' => $parent->abbreviation,
                'parent' => $child->uuid,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent');
    }

    public function test_structure_cannot_be_patched_with_its_descendant_as_parent()
    {
        $user = User::factory()->create();
        $parent = $this->createStructure('DPEF', 'Direction Projets', 'STRATEGIC');
        $child = $this->createStructure('BID', 'Projet BID', 'OPERATIONAL', $parent->uuid);

        $this->actingAs($user)
            ->patchJson("/api/structures/{$parent->id}", [
                'name' => $parent->name,
                'abbreviation' => $parent->abbreviation,
                'parent' => $child->uuid,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent');
    }

    public function test_structure_cannot_be_created_under_an_existing_cyclic_branch()
    {
        $user = User::factory()->create();
        $uas = $this->createStructure('UAS', 'UAS', 'VIRTUAL');
        $ops = $this->createStructure('UAS-OPS3', 'UAS OPS3', 'OPERATIONAL', $uas->uuid);
        $uas->update(['parent_uuid' => $ops->uuid]);

        $this->actingAs($user)
            ->postJson('/api/structures', [
                'name' => 'Nouvelle structure',
                'abbreviation' => 'NEW',
                'parent' => $uas->uuid,
                'type' => 'OPERATIONAL',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent');
    }

    private function createStructure(string $abbreviation, string $name, string $type, ?string $parentUuid = null): Structure
    {
        return Structure::create([
            'uuid' => Str::uuid(),
            'name' => $name,
            'abbreviation' => $abbreviation,
            'type' => $type,
            'parent_uuid' => $parentUuid,
            'status' => true,
        ]);
    }
}
