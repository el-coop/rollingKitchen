<?php

namespace App\Http\Controllers\ArtistManager;

use App\Http\Requests\Admin\Band\CreateBandRequest;
use App\Http\Requests\Admin\Band\DestroyBandRequest;
use App\Http\Requests\Admin\Band\UpdateBandRequest;
use App\Http\Requests\ArtistManager\StoreBandScheduleRequest;
use App\Models\Band;
use App\Models\BandSchedule;
use App\Models\Stage;
use Auth;
use Carbon\Carbon;
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
	
	public function index() {
		$schedules = BandSchedule::select('dateTime', 'stage_id as stage', 'band_id as band', 'payment', 'approved')->get()->groupBy('dateTime');
		$bands = Band::select('id')->with('user')->get()->pluck('user.name', 'id');
		$stages = Stage::select('id', 'name')->get()->pluck('name', 'id');
		$budget = app('settings')->get('schedule_budget');
		$initBudget = BandSchedule::sum('payment');
		$startDay = app('settings')->get('schedule_start_day');
		$days = Carbon::parse($startDay)->diffInDays(Carbon::parse(app('settings')->get('schedule_end_day'))) + 1;
		$startHour = app('settings')->get('schedule_start_hour');
		$endHour = app('settings')->get('schedule_end_hour');
		
		return view('artistManager.index', compact('bands', 'stages', 'schedules', 'budget', 'initBudget', 'startDay', 'days', 'startHour', 'endHour'));
	}
	
	public function storeSchedule(StoreBandScheduleRequest $request) {
		$request->commit();
		return [
			'success' => true
		];
	}
	
	public function create() {
		return (new Band)->fullData;
	}
	
	public function store(CreateBandRequest $request) {
		return $request->commit();
	}
	
	public function edit(Band $band) {
		return $band->fullData;
	}
	
	public function update(UpdateBandRequest $request, Band $band) {
		return $request->commit();
	}
	
	public function destroy(DestroyBandRequest $request, Band $band) {
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
