<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Kitchen\DeleteKitchenRequest;
use App\Http\Requests\Admin\Kitchen\UpdateKitchenRequest;
use App\Models\Kitchen;
use App\Models\User;
use Illuminate\Http\Request;

class KitchenController extends Controller {

	public function index() {
		$fieldType = 'Kitchen';
		$title = __('admin/kitchens.kitchens');
		return view('admin.datatableDefault', compact('fieldType','title'));
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
		$kitchen->load('photos', 'user', 'applications','applications.products','applications.invoices');
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
	 * @param DeleteKitchenRequest $request
	 * @param  \App\Models\Kitchen $kitchen
	 * @return void
	 */
	public function destroy(DeleteKitchenRequest $request,Kitchen $kitchen) {
		$request->commit();
	}

}
