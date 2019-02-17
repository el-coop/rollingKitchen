<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Kitchen\DeleteKitchenRequest;
use App\Http\Requests\Admin\Kitchen\UpdateKitchenRequest;
use App\Http\Requests\Kitchen\DestroyKitchenRequest;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class KitchenController extends Controller {
	
	public function index() {
		$fieldType = 'Kitchen';
		$title = __('admin/kitchens.kitchens');
		$deleteButton = true;
		return view('admin.datatableDefault', compact('fieldType', 'title', 'deleteButton'));
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
		$kitchen->load('photos', 'user', 'applications', 'applications.products', 'applications.invoices.payments', 'applications.electricDevices', 'applications.services');
		$indexLink = Kitchen::indexPage();
		return view('admin.kitchens.show', compact('kitchen', 'indexLink'));
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
	 * @param DestroyKitchenRequest $request
	 * @param  \App\Models\Kitchen $kitchen
	 * @return void
	 */
	public function destroy(DestroyKitchenRequest $request, Kitchen $kitchen) {
		$request->commit();
	}
	
}
