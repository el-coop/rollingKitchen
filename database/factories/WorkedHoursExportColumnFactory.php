<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\WorkedHoursExportColumn::class, function (Faker $faker) {
    return [
        'name' => $faker->title
    ];
});
