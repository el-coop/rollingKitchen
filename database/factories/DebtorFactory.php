<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Debtor::class, function (Faker $faker) {
	return [
		'name' => $faker->name,
		'email' => $faker->unique()->safeEmail,
		'language' => 'en',
		'data' => [
			'1' => 'something',
			'2' => 'something',
			'3' => 'something',
			'4' => 'something',
			'5' => 'something',
		]
	];
});
