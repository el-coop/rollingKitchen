<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BandAdmin::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
		'data' => [],
		'payment' => 0
    ];
});
