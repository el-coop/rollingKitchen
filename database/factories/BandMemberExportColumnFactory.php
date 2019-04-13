<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BandMemberExportColumn::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});
