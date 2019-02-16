<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TaxReview::class, function (Faker $faker) {
	return [
		'name' => 'tax review 2019',
		'file' => 'demo.pdf'
	];
});
