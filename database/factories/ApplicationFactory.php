<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $terrace = $this->faker->boolean;
        return [
            'status' => Arr::random(['pending', 'accepted', 'rejected']),
            'year' =>  $this->faker->year,
            'length' =>  $this->faker->randomFloat(2, 0, 15),
            'width' =>  $this->faker->randomFloat(2, 0, 15),
            'terrace_length' => $terrace ?  $this->faker->randomFloat(2, 0, 15) : null,
            'terrace_width' => $terrace ?  $this->faker->randomFloat(2, 0, 15) : null,
            'data' => []
        ];
    }
}
