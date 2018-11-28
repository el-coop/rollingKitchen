<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Service::class, function (Faker $faker) {
    return [
		'name_nl' => $faker->unique()->name,
		'name_en' => $faker->unique()->name,
		'category' => $faker->randomElement(['misc','electrical','safety']),
		'type' => $faker->randomElement([0, 1]),
		'price' => $faker->randomNumber(3)
    ];
});
