<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Application\UpdateApplicationRequest;
use App\Http\Requests\Admin\Application\UpdateApplicationServicesRequest;
use App\Http\Requests\Kitchen\CreateProductRequest;
use App\Http\Requests\Kitchen\UpdateDimensionsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Application;

class ApplicationController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$fieldType = 'Application';
		$filters = collect([
			'year' => app('settings')->get('registration_year')
		]);
		$title = __('admin/applications.applications');
		return view('admin.datatableDefault', compact('fieldType', 'filters', 'title'));
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
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Application $application) {

		$applicationIndex = $application->kitchen->applications()->orderBy('year', 'desc')->get()->search(function ($item) use ($application) {
			return $application->year == $item->year;
		});

		return redirect()->action('Admin\KitchenController@show', [
			'kitchen' => $application->kitchen,
			'tab' => __('admin/applications.applications'),
			'application' => $applicationIndex
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Application $application
	 * @return mixed
	 */
	public function edit(Application $application) {
		return $application->fullData;

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateApplicationRequest $request
	 * @param Application $application
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateApplicationRequest $request, Application $application) {
		return $request->commit();
	}

	public function updateDimensions(UpdateDimensionsRequest $request, Application $application) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function updateServices(UpdateApplicationServicesRequest $request, Application $application) {
		$request->commit();
		return [
			'success' => true
		];
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
