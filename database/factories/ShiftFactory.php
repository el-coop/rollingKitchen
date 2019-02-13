<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Shift::class, function (Faker $faker) {
	return [
		'date' => $faker->date(),
		'hours' => $faker->numberBetween(0, 50)
	];
});
