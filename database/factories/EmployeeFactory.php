<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_title' => substr($this->faker->jobTitle(), 0, 20),
            'structure_uuid' => null,
            'user_uuid' => null,
            'floor' => $this->faker->randomElement(['1', '2', 'RDC', 'B']),
            'office' => $this->faker->bothify('Bureau ##'),
            'can_logged_in' => $this->faker->boolean(80),
        ];
    }
}
