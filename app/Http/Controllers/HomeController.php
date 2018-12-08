<?php

namespace App\Http\Controllers;


class HomeController extends Controller {
	public function show() {
		$settings = app('settings');
		$locale = \App::getLocale();
		$registrationText = $settings->get("general_registration_text_{$locale}");
		$loginText = $settings->get("general_login_text_{$locale}");
		
		return view('welcome', compact('registrationText', 'loginText'));
	}
}
