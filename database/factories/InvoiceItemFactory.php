<?php

use Faker\Generator as Faker;

$factory->define(App\Models\InvoiceItem::class, function (Faker $faker) {
	return [
		'quantity' => $faker->numberBetween(1, 5),
		'unit_price' => $faker->numberBetween(1, 100),
		'name' => $faker->sentence
	];
});
