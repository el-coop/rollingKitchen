<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Error::class, function (Faker $faker) {
    return [
        'page' => $faker->title,
    ];
});
