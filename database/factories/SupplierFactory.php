<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'tax_number' => str_pad((string) $this->faker->unique()->numberBetween(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            'register_number' => $this->faker->optional()->bothify('RC-###/????'),
            'establishment_year' => $this->faker->optional()->year(),
            'capital' => $this->faker->randomFloat(2, 0, 100000000),
            'annual_turnover' => $this->faker->randomFloat(2, 0, 500000000),
            'employees_count' => $this->faker->numberBetween(1, 500),
            'status' => $this->faker->boolean(90),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'whatsapp' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
        ];
    }
}
