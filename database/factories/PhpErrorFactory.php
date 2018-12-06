<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\PhpError::class, function (Faker $faker) {
    return [
        'message' => $faker->text,
		'request' => json_encode([$faker->text => $faker->title, $faker->text => $faker->title]),
		'exception' => json_encode([$faker->text => $faker->title, $faker->text => $faker->title]),
	];
});
