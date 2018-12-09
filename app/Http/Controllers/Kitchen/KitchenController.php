<?php

namespace App\Http\Controllers\Kitchen;

use App;
use App\Http\Requests\Kitchen\CreateKitchenRequest;
use App\Http\Requests\Kitchen\DestroyKitchenRequest;
use App\Http\Requests\Kitchen\Photo\UploadPhotoRequest;
use App\Http\Requests\Kitchen\UpdateKitchenRequest;
use App\Models\Application;
use App\Models\Kitchen;
use App\Models\Pdf;
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
	 * @return \Illuminate\Http\RedirectResponse
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
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Kitchen $kitchen) {
		$locale = App::getLocale();
		
		$services = Service::where('category', '!=', 'socket')->orderBy('type', 'asc')->get();
		$sockets = Service::where('category', '=', 'socket')->orderBy('price', 'asc')->get();
		$application = $kitchen->getCurrentApplication();
		$pastApplications = $kitchen->applications()->where('year', '!=', app('settings')->get('registration_year'))->get();
		
		if ($application->status === 'accepted') {
			$pdfs = Pdf::where('visibility', 1)->orWhere('visibility', 2)->get();
		} else {
			$pdfs = Pdf::where('visibility', 1)->get();
		}
		
		$message = app('settings')->get("application_text_{$locale}");
		if (!$application->isOpen()) {
			$message = app('settings')->get("application_success_text_{$locale}");
		}
		return view('kitchen.edit', compact('kitchen', 'application', 'application', 'services', 'message', 'pastApplications', 'sockets', 'pdfs'));
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
		return back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess', [], $request->input('language'))
		]);
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param DestroyKitchenRequest $request
	 * @param  \App\Models\Kitchen $kitchen
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(DestroyKitchenRequest $request, Kitchen $kitchen) {
		$request->commit();
		return redirect()->action('HomeController@show');
	}
	
	public function destroyPhoto(Kitchen $kitchen, Photo $photo) {
		$photo->delete();
		return [
			'success' => true
		];
	}
}
