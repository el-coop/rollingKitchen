<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Field::class, function (Faker $faker) {
	return [
		'name' => $faker->unique()->name,
		'name_nl' => $faker->name,
		'type' => $faker->randomElement(['text', 'textarea']),
		'form' => \App\Models\Kitchen::class,
		'order' => $faker->unique()->numberBetween(0,100)
	];
});
