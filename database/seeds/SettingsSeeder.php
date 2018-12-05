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
		$settings->put('general_registration_status', false);
		$settings->put('general_registration_text_nl', 'Klik op aanmelden als je in 2017 niet hebt meegedaan aan Het Weekend van de Rollende keukens.');
		$settings->put('general_registration_text_en', 'Click register if you did not participate in Het Weekend van de Rollende Keukens in 2018');
		$settings->put('general_login_text_nl', 'Klik op inloggen als je in 2017 ook al hebt meegedaan aan Het Weekend van de Rollende Keukens.');
		$settings->put('general_login_text_en', 'Click login if you participated in Het Weekend van de Rollende Keukens in 2018');
		
		$settings->put('application_text_nl', $faker->paragraph);
		$settings->put('application_text_en', $faker->paragraph);
		$settings->put('application_success_text_nl', $faker->paragraph);
		$settings->put('application_success_text_en', $faker->paragraph);
		$settings->put('application_success_modal_nl', $faker->paragraph);
		$settings->put('application_success_modal_en', $faker->paragraph);
		$settings->put('application_success_email_nl', $faker->paragraph);
		$settings->put('application_success_email_en', $faker->paragraph);
		
		$settings->put('invoices_accountant', $faker->email);
		$settings->put('invoices_business_details', $faker->text);
		
		$settings->put('invoices_default_subject_nl', $faker->paragraph);
		$settings->put('invoices_default_subject_en', $faker->paragraph);
		$settings->put('invoices_default_email_nl', $faker->text);
		$settings->put('invoices_default_email_en', $faker->text);
		
		$settings->put('invoices_default_resend_subject_nl', $faker->paragraph);
		$settings->put('invoices_default_resend_subject_en', $faker->paragraph);
		$settings->put('invoices_default_resend_email_nl', $faker->text);
		$settings->put('invoices_default_resend_email_en', $faker->text);
		
		$settings->put('invoices_notes_nl', $faker->text);
		$settings->put('invoices_notes_en', $faker->text);
		$settings->put('invoices_footer_nl', $faker->text);
		$settings->put('invoices_footer_en', $faker->text);
		
	}
}
