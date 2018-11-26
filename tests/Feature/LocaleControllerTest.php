<?php

namespace Tests\Feature;

use App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocaleControllerTest extends TestCase {
	public function test_can_set_local_to_nl() {
		$this->get(action('LocaleController@set', [
			'language' => 'nl'
		]))->assertRedirect()->assertSessionHas('appLocale','nl');
	}
	
	public function test_uses_locale_from_session() {
		session()->put('appLocale','nl');
		
		$this->get(action('Auth\LoginController@login'))->assertSuccessful();
		
		$this->assertEquals('nl',App::getLocale());
	}
	
	public function test_defaults_locale_to_english() {
		session()->put('appLocale','dl');
		
		$this->get(action('Auth\LoginController@login'))->assertSuccessful();
		
		$this->assertEquals('en',App::getLocale());
	}
}
