<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller {
	public function set(Request $request, string $language) {
		if (array_key_exists($language, config('app.locales'))) {
			$request->session()->put('appLocale', $language);
		}
		return back();
	}
}
