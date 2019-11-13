<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Kitchen::class, function (Faker $faker) {
	return [
		'status' => Illuminate\Support\Arr::random(['new', 'motherlist']),
		'data' => []
	];
});
