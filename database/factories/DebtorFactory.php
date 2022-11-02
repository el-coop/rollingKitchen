<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debtor>
 */
class DebtorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'language' => 'en',
            'data' => [
                '1' => 'something',
                '2' => 'something',
                '3' => 'something',
                '4' => 'something',
                '5' => 'something',
            ]
        ];
    }
}
