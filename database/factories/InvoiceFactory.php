<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Invoice::class, function (Faker $faker) {
    return [
        'prefix' => app('settings')->get('registration_year'),
		'number' => \App\Models\Invoice::count(),
		'tax' => 21
    ];
});
