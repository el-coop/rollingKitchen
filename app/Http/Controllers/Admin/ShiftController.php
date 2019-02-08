<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shift\CreateShiftRequest;
use App\Http\Requests\Admin\Shift\UpdateShiftRequest;
use App\Models\Shift;
use App\Models\User;
use App\Models\WorkedHoursExportColumn;
use Illuminate\Http\Request;

class ShiftController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$title = __('worker/worker.shifts');
		$createTitle = __('admin/workers.createShift');
		$extraSlotView = 'admin.shift.manage';
		$withEditLink = false;
		$workedHoursOptions = WorkedHoursExportColumn::getOptionsAttribute();
		$buttons = ['<button class="button is-info" @click="$bus.$emit(' . "open-worked-hours" . ')">' . __('admin/shifts.exportWorkedHours') . '</button>'];
		return view('admin.datatableWithNew', compact('title', 'createTitle', 'extraSlotView', 'withEditLink', 'workedHoursOptions', 'buttons'));
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		return (new Shift)->fulldata;
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(CreateShiftRequest $request) {
		return $request->commit();
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Shift $shift
	 * @return \Illuminate\Http\Response
	 */
	public function show(Shift $shift) {
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Shift $shift
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Shift $shift) {
		
		return $shift->fulldata;
		
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Models\Shift $shift
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateShiftRequest $request, Shift $shift) {
		
		return $request->commit();
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Shift $shift
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Shift $shift) {
		//
	}
}
