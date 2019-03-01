<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Stage::class, function (Faker $faker) {
	return [
		'name' => $faker->unique()->name
	];
});
