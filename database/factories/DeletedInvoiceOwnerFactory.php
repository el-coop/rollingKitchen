<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\DeletedInvoiceOwner::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
		'email' => $faker->email,
		'language' => 'en'
    ];
});
