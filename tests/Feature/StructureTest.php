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
}