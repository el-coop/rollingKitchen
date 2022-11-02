<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'name_nl' => $this->faker->unique()->name,
            'name_en' => $this->faker->unique()->name,
            'category' => $this->faker->randomElement(['misc', 'electrical', 'safety']),
            'type' => $this->faker->randomElement([0, 1]),
            'price' => $this->faker->randomNumber(3)
        ];
    }
}
