<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shift\CreateShiftRequest;
use App\Http\Requests\Admin\Shift\DeleteAllShiftsRequest;
use App\Http\Requests\Admin\Shift\DestroyShiftRequest;
use App\Http\Requests\Admin\Shift\UpdateShiftRequest;
use App\Models\Field;
use App\Models\Shift;
use App\Models\WorkedHoursExportColumn;
use App\Models\Worker;
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
		
		$buttons = [
			view('admin.shift.deleteAllShifts'),
		];
		
		return view('admin.datatableWithNew', compact('title', 'createTitle', 'buttons', 'withEditLink', 'extraSlotView'));
	}
	
	
	public function deleteAll(DeleteAllShiftsRequest $request) {
		
		$request->commit();
		
		return back()->with('toast', [
			
			'type' => 'success',
			'title' => __('vue.deleteSuccess'),
			'message' => ' ',
		]);
		
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
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(CreateShiftRequest $request) {
		return $request->commit();
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Shift $shift
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Shift $shift) {
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Shift $shift
	 *
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
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateShiftRequest $request, Shift $shift) {
		
		return $request->commit();
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param DestroyShiftRequest $request
	 * @param  \App\Models\Shift $shift
	 *
	 * @return array
	 */
	public function destroy(DestroyShiftRequest $request, Shift $shift) {
		$request->commit();
		return [
			'success' => true
		];
	}
}
