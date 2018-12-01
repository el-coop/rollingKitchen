<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show(){
        $settings = app('settings');
    	$locale = \App::getLocale();
    	$registrationText = $settings->get("registration_text_{$locale}");
        $loginText = $settings->get("login_text_{$locale}");

    	return view ('welcome', compact('registrationText', 'loginText'));
	}
}
