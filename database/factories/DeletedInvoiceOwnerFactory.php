<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeletedInvoiceOwner>
 */
class DeletedInvoiceOwnerFactory extends Factory
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
            'email' => $this->faker->email,
            'language' => 'en',
            'data' => [
                1 => 'test',
                2 => 'test',
                3 => 'test',
                4 => 'test',
                5 => 'test',
            ]
        ];
    }
}
