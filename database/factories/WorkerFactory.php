<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Worker>
 */
class WorkerFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'type' => 0,
            'supervisor' => false,
            'approved' => false,
            'data' => [],
            'first_name' => $this->faker->firstName,
            'surname' => $this->faker->lastName
        ];
    }
}
