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
			}
		])->each(function ($kitchen) use ($faker) {
			$user = factory(User::class)->make([
				'name' => $faker->company
			]);
			$kitchen->user()->save($user);
			$imagesNumber = rand(0, 4);
			
			for ($i = 0; $i < $imagesNumber; $i++) {
				$kitchen->photos()->save(factory(Photo::class)->make());
				$kitchen->applications()->save(factory(Application::class)->make(['year' => 2015 + $i]));
			}
			
			$kitchen->applications->each(function ($application) use ($faker) {
				$application->data = Application::fields()->mapWithKeys(function ($field) use ($faker) {
					if ($field->type === 'text') {
						$value = $faker->name;
					} else {
						$value = $faker->paragraph;
					}
					
					return [$field->name => $value];
					
				});
				$application->save();
				
				$products = rand(1, 4);
				for ($j = 0; $j < $products; $j++) {
					$application->products()->save(factory(\App\Models\Product::class)->make());
				}
			});
			
		});
	}
}
