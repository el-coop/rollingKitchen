<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller {
	public function show() {
		$settings = app('settings');
		$locale = \App::getLocale();
		$registrationOpen = $settings->get('general_registration_status');
		if ($registrationOpen) {
			$registrationText = $settings->get("general_registration_text_{$locale}");
		} else {
			$registrationText = $settings->get("general_registration_closed_text_{$locale}");

		}
		$loginText = $settings->get("general_login_text_{$locale}");

		return view('welcome', compact('registrationText', 'loginText', 'registrationOpen'));
	}
}
