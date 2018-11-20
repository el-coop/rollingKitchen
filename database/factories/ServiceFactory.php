<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Service::class, function (Faker $faker) {
    return [
		'name' => $faker->unique()->name,
		'type' => $faker->randomElement(['overige;','elektra','veiligheid']),
		'price' => $faker->randomNumber(3)
    ];
});
