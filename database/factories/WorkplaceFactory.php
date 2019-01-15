<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Workplace::class, function (Faker $faker) {
    return [
        'name' => $faker->company
    ];
});
