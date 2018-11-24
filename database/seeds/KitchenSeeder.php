<?php

use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class KitchenSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @param Faker $faker
	 * @return void
	 */
	public function run(Faker $faker) {
		factory(Kitchen::class, 25)->create([
			'data' => function () use ($faker) {
				return Kitchen::fields()->mapWithKeys(function ($field) use ($faker) {
					if ($field->type === 'text') {
						$value = $faker->name;
					} else {
						$value = $faker->paragraph;
					}
					
					return [$field->name => $value];
					
				});
			},
		])->each(function ($kitchen) use ($faker) {
			$user = factory(User::class)->make([
				'name' => $faker->company
			]);
			$kitchen->user()->save($user);
		});
	}
}
