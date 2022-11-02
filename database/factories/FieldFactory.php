<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name_en' => $this->faker->unique()->name,
            'name_nl' => $this->faker->name,
            'type' => $this->faker->randomElement(['text', 'textarea']),
            'form' => \App\Models\Kitchen::class,
            'order' => $this->faker->unique()->numberBetween(0,100),
            'status' => 'protected'
        ];
    }
}
