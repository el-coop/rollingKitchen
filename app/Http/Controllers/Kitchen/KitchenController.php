<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Requests\Kitchen\CreateKitchenRequest;
use App\Http\Requests\Kitchen\Photo\UploadPhotoRequest;
use App\Http\Requests\Kitchen\UpdateKitchenRequest;
use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Photo;
use App\Models\Service;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KitchenController extends Controller {
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		return view('auth.register');
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param CreateKitchenRequest $request
	 * @return void
	 */
	public function store(CreateKitchenRequest $request) {
		$kitchen = $request->commit();
		Auth::login($kitchen->user, true);
		return redirect()->action('Kitchen\KitchenController@edit', $kitchen);
	}
	
	public function storePhoto(Kitchen $kitchen, UploadPhotoRequest $request) {
		return $request->commit();
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function show(Kitchen $kitchen) {
		return view('kitchen.edit', compact('kitchen'));
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Kitchen $kitchen) {
		$services = Service::all();
		$application = $kitchen->getCurrentApplication();
		if ($application->status != 'new') {
			return redirect()->action('Kitchen\KitchenController@show', $kitchen);
		}
		return view('kitchen.edit', compact('kitchen', 'application', 'application', 'services'));
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateKitchenRequest $request, Kitchen $kitchen) {
		$request->commit();
		return back()->with('success', true);
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
	
	public function destroyPhoto(Kitchen $kitchen, Photo $photo) {
		$photo->delete();
		return [
			'success' => true
		];
	}
}
