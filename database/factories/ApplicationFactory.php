<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Application::class, function (Faker $faker) {
    return [
        'status' => array_random(['pending', 'accepted', 'rejected']),
        'year' => $faker->year
    ];
});
