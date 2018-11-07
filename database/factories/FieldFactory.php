<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Field::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'type' => $faker->randomElement(['text', 'textarea'])
    ];
});
