<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+3 years');

        return [
            'supplier_uuid' => Supplier::inRandomOrder()->value('uuid'),
            'contract_number' => strtoupper($this->faker->unique()->bothify('CT-###/####')),
            'title' => $this->faker->sentence(6),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $this->faker->randomFloat(2, 1000, 10000000),
            'description' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->boolean(90),
            'signed_at' => $this->faker->dateTimeBetween('-2 years', $startDate)->format('Y-m-d'),

        ];
    }
}
