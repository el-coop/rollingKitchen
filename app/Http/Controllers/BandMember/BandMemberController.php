<?php

namespace App\Http\Controllers\BandMember;

use App\Http\Requests\BandMember\UpdateBandMemberRequest;
use App\Models\BandMember;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Password;
use Auth;
use App\Http\Controllers\Controller;

class BandMemberController extends Controller {

	use ResetsPasswords;

	public function showResetForm(Request $request, $token = null) {
		return view('worker.setPassword')->with(
			['token' => $token, 'email' => $request->email]
		);
	}

	public function show(BandMember $bandMember){
		return view('bandMember.show', compact('bandMember'));
	}

	public function update(UpdateBandMemberRequest $request, BandMember $bandMember){
		$request->commit();
		return back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess', [], $request->input('language'))
		]);
	}

	public function broker() {
		return Password::broker('workers');
	}


	public function redirectTo() {
		return Auth::user()->user->homePage();
	}
}
