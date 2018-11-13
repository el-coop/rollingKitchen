<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Field::class, function (Faker $faker) {
	return [
		'name' => $faker->unique()->name,
		'type' => $faker->randomElement(['text', 'textarea']),
		'form' => \App\Models\Kitchen::class,
		'order' => $faker->unique()->randomElement([1, 2, 3, 4, 5])
	];
});
