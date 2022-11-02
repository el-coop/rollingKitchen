<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BandSchedule>
 */
class BandScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date_time' => $this->faker->date(),
            'payment' => $this->faker->numberBetween(0, 50),
            'approved' => $this->faker->boolean,
            'end_time' => $this->faker->date()
        ];
    }
}
