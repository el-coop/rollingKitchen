<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show(){

    	$locale = \App::getLocale();
    	$registrationText = Setting::where('name', "registration_text_{$locale}")->first()->value;
		$loginText = Setting::where('name', "login_text_{$locale}")->first()->value;

    	return view ('welcome', compact('registrationText', 'loginText'));
	}
}
