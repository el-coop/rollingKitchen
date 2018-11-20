<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Kitchen\UpdateKitchenRequest;
use App\Models\Kitchen;
use App\Models\User;
use Illuminate\Http\Request;

class KitchenController extends Controller {
	
	public function index() {
		$fieldType = 'Kitchen';
		return view('admin.datatableDefault', compact('fieldType'));
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		return true;
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function show(Kitchen $kitchen) {
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Kitchen $kitchen) {
		
		return $kitchen->fullData;
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateKitchenRequest $request, Kitchen $kitchen) {
		return $request->commit();
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Kitchen $kitchen) {
		//
	}
	
}
