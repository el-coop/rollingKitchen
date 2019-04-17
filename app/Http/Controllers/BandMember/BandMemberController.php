<?php

namespace App\Http\Controllers\BandMember;

use App;
use App\Http\Requests\BandMember\StorePhotoRequest;
use App\Http\Requests\BandMember\UpdateBandMemberRequest;
use App\Models\BandMember;
use App\Models\BandMemberPhoto;
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
	
	public function show(BandMember $bandMember) {
		$locale = App::getLocale();
		$privacyStatement = str_replace(PHP_EOL, '<br>', app('settings')->get("band_members_privacy_statement_{$locale}"));
		
		return view('bandMember.show', compact('bandMember', 'privacyStatement'));
	}
	
	public function update(UpdateBandMemberRequest $request, BandMember $bandMember) {
		$request->commit();
		return back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess', [], $request->input('language'))
		]);
	}
	
	public function storePhoto(BandMember $bandMember, StorePhotoRequest $request) {
		return $request->commit();
	}
	
	public function destroyPhoto(BandMember $bandMember, BandMemberPhoto $photo) {
		$photo->delete();
		return [
			'success' => true
		];
	}
	
	public function broker() {
		return Password::broker('workers');
	}
	
	
	public function redirectTo() {
		return Auth::user()->user->homePage();
	}
}
