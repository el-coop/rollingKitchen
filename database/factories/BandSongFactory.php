<?php

use Faker\Generator as Faker;

$factory->define(App\Models\BandSong::class, function (Faker $faker) {
	return [
		'title' => $faker->name(),
		'composer' => $faker->name(),
		'owned' => $faker->boolean(),
		'protected' => $faker->boolean(),
	];
});
