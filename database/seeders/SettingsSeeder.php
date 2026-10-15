<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;

class SettingsSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run(Faker $faker) {
        $this->settingsFakeNoOverwrite('registration_year', false);
        $this->settingsFakeNoOverwrite('general_registration_closed_text_nl', 'Het is helaas niet langer mogelijk om je aan te melden voor de Rolende Keukens 2019.');
        $this->settingsFakeNoOverwrite('general_registration_closed_text_en', 'Unfortunately it is no longer possible to register for the Rollende Keukens 2019.');

        $this->settingsFakeNoOverwrite('application_text_nl', $faker->paragraph);
        $this->settingsFakeNoOverwrite('application_text_en', $faker->paragraph);
        $this->settingsFakeNoOverwrite('application_success_text_nl', $faker->paragraph);
        $this->settingsFakeNoOverwrite('application_success_text_en', $faker->paragraph);
        $this->settingsFakeNoOverwrite('application_success_email_nl', $faker->paragraph);
        $this->settingsFakeNoOverwrite('application_success_email_en', $faker->paragraph);

        $this->settingsFakeNoOverwrite('invoices_business_details', $faker->text);
        $this->settingsFakeNoOverwrite('invoices_footer_nl', $faker->text);
        $this->settingsFakeNoOverwrite('invoices_footer_en', $faker->text);

        $this->settingsFakeNoOverwrite('workers_liability_popup_en', $faker->paragraph);
        $this->settingsFakeNoOverwrite('workers_liability_popup_nl', $faker->paragraph);

        $this->settingsFakeNoOverwrite('bands_text_en', $faker->paragraph);
        $this->settingsFakeNoOverwrite('bands_text_nl', $faker->paragraph);

        $this->settingsFakeNoOverwrite('accountant_email', $faker->email);
        $this->settingsFakeNoOverwrite('accountant_password', '');

        $this->settingsFakeNoOverwrite('schedule_start_day', \Carbon\Carbon::now()->addMonths(3));
        $this->settingsFakeNoOverwrite('schedule_end_day', \Carbon\Carbon::now()->addMonths(3)->addWeek());

        $this->settingsFakeNoOverwrite('schedule_deleted_nl', $faker->paragraph);
        $this->settingsFakeNoOverwrite('schedule_deleted_en', $faker->paragraph);

        foreach (config('settingsDefaults') as $key => $value) {
            $this->settingsFakeNoOverwrite($key, $value);
        }

    }

    protected function settingsFakeNoOverwrite($key, $value) {
        $settings = app('settings');

        if ($settings->get($key) === null) {
            $settings->put($key, $value);
        }
    }
}
