<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ElectricDevice::class, function (Faker $faker) {
	return [
		'name' => $faker->name,
		'watts' => $faker->randomNumber()
	];
});
