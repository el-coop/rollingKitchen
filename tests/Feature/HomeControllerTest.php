<?php

namespace Tests\Feature;

use ElCoop\Valuestore\Valuestore;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase {

	use WithFaker;

	public function setUp(): void {
		parent::setUp();

		Storage::fake('local');
		Storage::disk('local')->put('test.valuestore.json', '');
		$path = Storage::path('test.valuestore.json');
		$this->app->singleton('settings', function ($app) use ($path) {
			return new Valuestore($path);
		});
		$faker = $this->faker;
		$settings = app('settings');
		$settings->put('general_registration_status', true);
		$settings->put('registration_year', 2018);
		$settings->put('general_registration_text_en', $faker->text);
		$settings->put('general_login_text_en', $faker->text);




	}

	public function test_homepage_works(){


		$this->get(action('HomeController@show'))->assertSuccessful()->assertViewIs('welcome');
	}

	public function test_shows_registration_open_text(){

		$settings = app('settings');
		$locale = \App::getLocale();
		$settings->put("general_registration_text_{$locale}", 'RegistrationOpen');
		$this->get(action('HomeController@show'))->assertSuccessful()->assertSee('RegistrationOpen');

	}

	public function test_shows_registration_closed_text(){

		$settings = app('settings');
		$settings->put('general_registration_status', false);
		$locale = \App::getLocale();
		$settings->put("general_registration_closed_text_{$locale}", 'RegistrationClosed');
		$this->get(action('HomeController@show'))->assertSuccessful()->assertSee('RegistrationClosed');

	}
}
