<?php

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
        $settings = app('settings');
        $settings->put('accountant', $faker->email);
        $settings->put('registration_status', false);
        $settings->put('application_text_en', $faker->text);
        $settings->put('application_text_nl', $faker->text);
		$settings->put('registration_text_nl', $faker->text);
        $settings->put('registration_text_en', $faker->text);
        $settings->put('login_text_en', $faker->text);
        $settings->put('login_text_nl', $faker->text);
    }
}
