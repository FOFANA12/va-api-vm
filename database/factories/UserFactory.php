<?php

namespace Database\Factories;

use App\Support\Language;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'lang' => $this->faker->randomElement(Language::codes()),
            'phone' => $this->faker->unique()->phoneNumber(),
            'status' => true,
            'password' => Hash::make('password'),
            'avatar' => null,
            'role_uuid' => null,
        ];
    }

    public function withoutLogin(): static
    {
        return $this->state(fn () => [
            'email' => null,
            'password' => null,
        ]);
    }
}
