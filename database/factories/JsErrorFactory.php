<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JsError>
 */
class JsErrorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'vm' => json_encode(['vm']),
            'message' => $this->faker->text,
            'exception' => json_encode([$this->faker->text => $this->faker->title, $this->faker->text => $this->faker->title]),
            'user_agent' => $this->faker->userAgent
        ];
    }
}
