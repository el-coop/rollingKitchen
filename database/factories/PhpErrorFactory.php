<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhpError>
 */
class PhpErrorFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'message' => $this->faker->text,
            'request' => json_encode([$this->faker->text => $this->faker->title, $this->faker->text => $this->faker->title]),
            'exception' => json_encode([$this->faker->text => $this->faker->title, $this->faker->text => $this->faker->title]),
        ];
    }
}
