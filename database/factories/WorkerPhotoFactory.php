<?php

use Faker\Generator as Faker;

$factory->define(App\Models\WorkerPhoto::class, function (Faker $faker) {
	return [
		'file' => 'demo.jpg'
	];
});
