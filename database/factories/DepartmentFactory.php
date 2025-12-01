<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       
        $region = Region::inRandomOrder()->first();

        if (!$region) {
            $region = Region::factory()->create();
        }
        
        return [
            'name' => $this->faker->unique()->city(),
            'region_uuid' => $region->uuid,
            'status' => $this->faker->boolean(100),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}
