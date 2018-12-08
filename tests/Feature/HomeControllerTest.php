<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Spatie\Valuestore\Valuestore;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase {

	public function setUp() {
		parent::setUp();

		Storage::fake('local');
		Storage::disk('local')->put('test.valuestore', '');
		$path = Storage::path('test.valuestore');
		$this->app->singleton('settings', function ($app) use ($path) {
			return Valuestore::make($path . '.json');
		});
		$settings = app('settings');
		$settings->put('general_registration_status', true);
		$this->app->settings->put('registration_year', 2018);



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
