<?php

namespace App\Http\Controllers\Band;

use App\Http\Requests\Band\ApproveScheduleRequest;
use App\Http\Requests\Band\CreateBandMemberRequest;
use App\Http\Requests\Band\DestroyBandMemberRequest;
use App\Http\Requests\Band\RejectScheduleRequest;
use App\Http\Requests\Band\UpdateBandMemberRequest;
use App\Http\Requests\Band\UpdateBandRequest;
use App\Http\Requests\Band\UpdatePaymentMethodRequest;
use App\Models\Band;
use App\Models\BandSchedule;
use Auth;
use App\Models\BandMember;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Password;
use App\Http\Controllers\Controller;

class BandController extends Controller {

	use ResetsPasswords;

	public function showResetForm(Request $request, $token = null) {
		return view('worker.setPassword')->with(
			['token' => $token, 'email' => $request->email]
		);
	}

	public function show(Band $band) {
		return view('band.band', compact('band'));
	}

	public function update(UpdateBandRequest $request, Band $band){
		$request->commit();
		return back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess', [], $request->input('language'))
		]);
	}

	public function addBandMember(CreateBandMemberRequest $request, Band $band){
		return $request->commit();
	}

	public function updateBandMember(UpdateBandMemberRequest $request, Band $band, BandMember $bandMember){
		return $request->commit();
	}

	public function destroyBandMember(DestroyBandMemberRequest $request, Band $band, BandMember $bandMember){
		$request->commit();

		return [
			'success' => true
		];
	}

	public function approveSchedule(ApproveScheduleRequest $request, Band $band, BandSchedule $bandSchedule){
		$request->commit();
	}

	public function rejectSchedule(RejectScheduleRequest $request, Band $band, BandSchedule $bandSchedule){
		$request->commit();
	}

	public function broker() {
		return Password::broker('workers');
	}


	public function redirectTo() {
		return Auth::user()->user->homePage();
	}
}
