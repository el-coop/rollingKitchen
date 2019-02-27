<?php

namespace App\Http\Controllers\Band;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BandController extends Controller {

	use ResetsPasswords;

	public function showResetForm(Request $request, $token = null) {
		return view('worker.setPassword')->with(
			['token' => $token, 'email' => $request->email]
		);
	}
}
