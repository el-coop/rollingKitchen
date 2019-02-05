<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Worker::class, function (Faker $faker) {
	return [
		'type' => 0,
		'supervisor' => false,
		'approved' => false,
		'data' => []
	];
});
