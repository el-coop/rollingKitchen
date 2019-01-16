<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\WorkFunction::class, function (Faker $faker) {
    return [
        'name' => $faker->jobTitle,
		'payment_per_hour_before_tax' => $faker->randomNumber(2),
		'payment_per_hour_after_tax' => $faker->randomNumber(2)
    ];
});
