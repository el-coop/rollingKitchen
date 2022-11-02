<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkFunction>
 */
class WorkFunctionFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'name' => $this->faker->jobTitle,
            'payment_per_hour_before_tax' => $this->faker->randomNumber(2),
            'payment_per_hour_after_tax' => $this->faker->randomNumber(2)
        ];
    }
}
