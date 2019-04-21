<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BandAdminPhoto::class, function (Faker $faker) {
    return [
		'file' => 'demo.jpg'

	];
});
