<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Application::class, function (Faker $faker) {
	
	$terrace = $faker->boolean;
	
	return [
		'status' => array_random(['pending', 'accepted', 'rejected']),
		'year' => $faker->year,
		'length' => $faker->randomFloat(2, 0, 15),
		'width' => $faker->randomFloat(2, 0, 15),
		'terrace_length' => $terrace ? $faker->randomFloat(2, 0, 15) : null,
		'terrace_width' => $terrace ? $faker->randomFloat(2, 0, 15) : null,
		'data' => []
	];
});
