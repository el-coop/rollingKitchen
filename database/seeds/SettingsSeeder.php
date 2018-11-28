<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;

class SettingsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker) {
        factory(Setting::class)->create([
            'name' => 'accountant',
            'value' => $faker->email
        ]);
        factory(Setting::class)->create([
            'name' => 'registration_status',
            'value' => 0
        ]);
        factory(Setting::class)->create([
            'name' => 'application_text_en',
            'value' => $faker->text
        ]);
        factory(Setting::class)->create([
            'name' => 'application_text_nl',
            'value' => $faker->text
        ]);

		factory(Setting::class)->create([
			'name' => 'registration_text_nl',
			'value' => $faker->text
		]);

		factory(Setting::class)->create([
			'name' => 'registration_text_en',
			'value' => $faker->text
		]);

		factory(Setting::class)->create([
			'name' => 'login_text_nl',
			'value' => $faker->text
		]);

		factory(Setting::class)->create([
			'name' => 'login_text_en',
			'value' => $faker->text
		]);
    }
}
