<?php

namespace App\Http\Controllers\ArtistManager;

use App\Http\Requests\Admin\Band\CreateBandRequest;
use App\Http\Requests\Admin\Band\DestroyBandRequest;
use App\Http\Requests\Admin\Band\UpdateBandRequest;
use App\Models\Band;
use Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArtistManagerController extends Controller {

	use ResetsPasswords;

	public function showResetForm(Request $request, $token = null) {
		return view('worker.setPassword')->with(
			['token' => $token, 'email' => $request->email]
		);
	}

	public function index(){
		return view('artistManager.index');
	}

	public function create(){
		return (new Band)->fullData;
	}

	public function store(CreateBandRequest $request){
		return $request->commit();
	}

	public function edit(Band $band){
		return $band->fullData;
	}

	public function update(UpdateBandRequest $request, Band $band){
		return $request->commit();
	}

	public function destroy(DestroyBandRequest $request, Band $band){
		$request->commit();

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
