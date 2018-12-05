<?php

use App\Models\Application;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ApplicationSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run(Faker $faker) {
		\App\Models\Kitchen::all()->each(function ($kitchen) use ($faker) {
			$applicationNumber = rand(0, 4);

			for ($i = 0; $i < $applicationNumber; $i++) {
				$application = factory(Application::class)->make(['year' => 2015 + $i]);
				$application->data = Application::fields()->mapWithKeys(function ($field) use ($faker) {
					if ($field->type === 'text') {
						$value = $faker->name;
					} else {
						$value = $faker->paragraph;
					}
					return [$field->id => $value];
				});
				$kitchen->applications()->save($application);
                \App\Models\Service::inRandomOrder()->limit(3)->get()->each(function ($service) use ($application) {
                   $application->services()->save($service, ['quantity' => random_int(1,5)]);
                });
			}
		});
	}
}
