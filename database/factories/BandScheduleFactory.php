<?php

use Faker\Generator as Faker;

$factory->define(App\Models\BandSchedule::class, function (Faker $faker) {
	return [
		'date_time' => $faker->date(),
		'payment' => $faker->numberBetween(0, 50),
		'approved' => $faker->boolean,
		'end_time' => $faker->date()
	];
});
