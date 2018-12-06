<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\JsError::class, function (Faker $faker) {
    return [
        'vm' => json_encode(['vm']),
		'message' => $faker->text,
		'exception' => json_encode([$faker->text => $faker->title, $faker->text => $faker->title]),
		'user_agent' => $faker->userAgent
	];
});
