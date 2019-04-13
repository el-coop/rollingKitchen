<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BandMember::class, function (Faker $faker) {
	return [
		'data' => [],
		'payment' => 0
	];
});
