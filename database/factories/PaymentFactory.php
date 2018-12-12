<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\InvoicePayment::class, function (Faker $faker) {
    return [
        'amount' => $faker->numberBetween(10,200),
		'date' => $faker->date()
    ];
});
