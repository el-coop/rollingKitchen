<?php

use Faker\Generator as Faker;

$factory->define(App\Models\BandSchedule::class, function (Faker $faker) {
	return [
		'dateTime' => $faker->date(),
		'payment' => $faker->numberBetween(0, 50),
		'approved' => $faker->boolean
	];
});
