<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BandSong>
 */
class BandSongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name(),
            'composer' => $this->faker->name(),
            'owned' => $this->faker->boolean(),
            'protected' => $this->faker->boolean(),
        ];
    }
}
