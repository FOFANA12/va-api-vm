<?php

namespace Database\Factories;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractTtype>
 */
class ContractTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        
        return [
            'name' => $this->faker->unique()->city(),
            'status' => $this->faker->boolean(100),
        ];
    }
}
