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

	public function create() {

		return (new Service)->fulldata;
	}

	public function store(CreateServiceRequest $request) {

		return $request->commit();

	}

	public function edit(Service $service) {

		return $service->fulldata;
	}

	public function update(UpdateServiceRequest $request, Service $service) {
		return $request->commit();
	}

}
