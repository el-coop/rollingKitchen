<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Service\CreateServiceRequest;
use App\Http\Requests\Admin\Service\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller {
	public function index() {

		return view('admin.services.index');

	}

	public function show (){

		return collect([[
			'name' => 'name',
			'label' => __('misc.name'),
			'type' => 'text',
			'value' => '',
		], [
			'name' => 'type',
			'label' => __('misc.type'),
			'type' => 'select',
			'options' => [
				'safety' => __('services.safety'),
				'electrical' => __('services.electrical'),
				'misc' => __('services.misc'),
			],
			'value' => '',
		], [
			'name' => 'price',
			'label' => __('misc.price'),
			'type' => 'text',
			'value' => '',
		]]);
	}

	public function create (CreateServiceRequest $request){

		return $request->commit();


	}

	public function edit(Service $service) {

		return $service->fulldata;
	}

	public function update(UpdateServiceRequest $request, Service $service) {
		return $request->commit();
	}

}
