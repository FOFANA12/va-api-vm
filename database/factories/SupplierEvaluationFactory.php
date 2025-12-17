<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierEvaluation>
 */
class SupplierEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scoreDelay = $this->faker->randomFloat(2, 0, 5);
        $scorePrice = $this->faker->randomFloat(2, 0, 5);
        $scoreDelay = $this->faker->randomFloat(2, 0, 5);
        $scoreQuality = $this->faker->randomFloat(2, 0, 5);
        $scoreDelay = $this->faker->randomFloat(2, 0, 5);
        return [
            'supplier_uuid' => Supplier::query()->inRandomOrder()->value('uuid'),
            'score_delay' => $scoreDelay,
            'score_price' => $scorePrice,
            'score_quality' => $scoreQuality,
            'evaluated_at' => $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'comment' => $this->faker->text(100),
            'total_score' => ((($scoreDelay * 4) + ($scorePrice * 2) + ($scoreQuality * 4)) / 10),

        ];
    }
}
