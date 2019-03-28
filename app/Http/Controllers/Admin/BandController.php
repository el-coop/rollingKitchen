<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Band\CreateBandRequest;
use App\Http\Requests\Admin\Band\DestroyBandRequest;
use App\Http\Requests\Admin\Band\SendConfirmationRequest;
use App\Http\Requests\Admin\Band\UpdateBandRequest;
use App\Http\Requests\ArtistManager\StoreBandScheduleRequest;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\BandSchedule;
use App\Models\Stage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BandController extends Controller {
	
	public function index() {
		$title = __('admin/artists.bands');
		$fieldType = "Band";
		$createTitle = __('admin/artists.createBand');
		$buttons = [
			'<a class="button is-light" href="' . action('Admin\FieldController@index', 'BandMember') . '">' . __('admin/bandMembers.fields') . '</a>',
			'<ajax-form action="' . action('Admin\BandController@sendConfirmation') . '">
				<button class="button is-success">' . __('admin/bands.sendConfirmation') . '</button>
			</ajax-form>'
			];
		return view('admin.datatableWithNew', compact('title', 'createTitle', 'fieldType', 'buttons'));
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
	
	public function nonAjaxUpdate(UpdateBandRequest $request, Band $band) {
		$request->commit();
		return back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess', [], $request->input('language'))
		]);
	}
	
	public function destroy(DestroyBandRequest $request, Band $band) {
		$request->commit();
		
		return [
			'success' => true
		];
	}
	
	public function show(Band $band) {
		return view('admin.bands.band', compact('band'));
	}

	public function sendConfirmation(SendConfirmationRequest $request){
		$request->commit();
	}
	
	public function schedule() {
		$schedules = BandSchedule::select('date_time', 'stage_id as stage', 'band_id as band', 'payment', 'approved')->get()->groupBy('date_time');
		$bands = Band::select('id')->with('user')->get()->pluck('user.name', 'id');
		$stages = Stage::select('id', 'name')->get()->pluck('name', 'id');
		$budget = app('settings')->get('schedule_budget');
		$initBudget = BandSchedule::sum('payment');
		$startDay = app('settings')->get('schedule_start_day');
		$days = Carbon::parse($startDay)->diffInDays(Carbon::parse(app('settings')->get('schedule_end_day'))) + 1;
		$startHour = app('settings')->get('schedule_start_hour');
		$endHour = app('settings')->get('schedule_end_hour');
		
		
		return view('admin.bands.schedule', compact('bands', 'stages', 'schedules', 'budget', 'initBudget', 'startDay', 'startHour', 'days', 'endHour'));
	}
	
	public function storeSchedule(StoreBandScheduleRequest $request) {
		$request->commit();
		return [
			'success' => true
		];
	}
}
