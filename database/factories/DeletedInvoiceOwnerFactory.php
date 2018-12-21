<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\DeletedInvoiceOwner::class, function (Faker $faker) {
	return [
		'name' => $faker->name,
		'email' => $faker->email,
		'language' => 'en',
		'data' => [
			1 => 'test',
			2 => 'test',
			3 => 'test',
			4 => 'test',
			5 => 'test',
		]
	];
});
