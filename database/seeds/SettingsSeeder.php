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
		$this->settingsFakeNoOverwrite('registration_year', false);
		$this->settingsFakeNoOverwrite('general_registration_status', false);
		$this->settingsFakeNoOverwrite('general_registration_text_nl', 'Klik op aanmelden als je in 2017 niet hebt meegedaan aan Het Weekend van de Rollende keukens.');
		$this->settingsFakeNoOverwrite('general_registration_text_en', 'Click register if you did not participate in Het Weekend van de Rollende Keukens in 2018');
		$this->settingsFakeNoOverwrite('general_registration_closed_text_nl', 'Het is helaas niet langer mogelijk om je aan te melden voor de Rolende Keukens 2019.');
		$this->settingsFakeNoOverwrite('general_registration_closed_text_en', 'Unfortunately it is no longer possible to register for the Rollende Keukens 2019.');
		$this->settingsFakeNoOverwrite('general_login_text_nl', 'Klik op inloggen als je in 2017 ook al hebt meegedaan aan Het Weekend van de Rollende Keukens.');
		$this->settingsFakeNoOverwrite('general_login_text_en', 'Click login if you participated in Het Weekend van de Rollende Keukens in 2018');
		
		$this->settingsFakeNoOverwrite('application_text_nl', $faker->paragraph);
		$this->settingsFakeNoOverwrite('application_text_en', $faker->paragraph);
		$this->settingsFakeNoOverwrite('application_success_text_nl', $faker->paragraph);
		$this->settingsFakeNoOverwrite('application_success_text_en', $faker->paragraph);
		$this->settingsFakeNoOverwrite('application_success_modal_nl', $faker->paragraph);
		$this->settingsFakeNoOverwrite('application_success_modal_en', $faker->paragraph);
		$this->settingsFakeNoOverwrite('application_success_email_nl', $faker->paragraph);
		$this->settingsFakeNoOverwrite('application_success_email_en', $faker->paragraph);
		
		$this->settingsFakeNoOverwrite('invoices_business_details', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_default_subject_nl', $faker->paragraph);
		$this->settingsFakeNoOverwrite('invoices_default_subject_en', $faker->paragraph);
		$this->settingsFakeNoOverwrite('invoices_default_email_nl', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_default_email_en', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_default_resend_subject_nl', $faker->paragraph);
		$this->settingsFakeNoOverwrite('invoices_default_resend_subject_en', $faker->paragraph);
		$this->settingsFakeNoOverwrite('invoices_default_resend_email_nl', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_default_resend_email_en', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_notes_nl', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_notes_en', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_footer_nl', $faker->text);
		$this->settingsFakeNoOverwrite('invoices_footer_en', $faker->text);
		
		$this->settingsFakeNoOverwrite('workers_user_created_subject_nl', $faker->sentence);
		$this->settingsFakeNoOverwrite('workers_user_created_subject_en', $faker->sentence);
		$this->settingsFakeNoOverwrite('workers_user_created_nl', $faker->paragraph);
		$this->settingsFakeNoOverwrite('workers_user_created_en', $faker->paragraph);
		$this->settingsFakeNoOverwrite('workers_tax_review_uploaded_subject_nl', $faker->sentence);
		$this->settingsFakeNoOverwrite('workers_tax_review_uploaded_subject_en', $faker->sentence);
		$this->settingsFakeNoOverwrite('workers_tax_review_uploaded_en', $faker->paragraph);
		$this->settingsFakeNoOverwrite('workers_tax_review_uploaded_nl', $faker->paragraph);
		
		$this->settingsFakeNoOverwrite('accountant_email', $faker->email);
		$this->settingsFakeNoOverwrite('accountant_password', '');
		
		
	}
	
	protected function settingsFakeNoOverwrite($key, $value) {
		$settings = app('settings');
		
		if ($settings->get($key) === null) {
			$settings->put($key, $value);
		}
	}
}
