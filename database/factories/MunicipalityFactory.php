<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Municipality>
 */
class MunicipalityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       
        $department = Department::inRandomOrder()->first();

        if (!$department) {
            $department = Department::factory()->create();
        }

        return [
            'name' => $this->faker->unique()->city(),
            'department_uuid' => $department->uuid,
            'status' => $this->faker->boolean(100),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}
