<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Requests\Kitchen\CreateDeviceRequest;
use App\Http\Requests\Kitchen\UpdateDeviceRequest;
use App\Models\Application;
use App\Models\ElectricDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationDeviceController extends Controller {
	public function create(Application $application, CreateDeviceRequest $request) {
		return $request->commit();
	}
	
	public function update(UpdateDeviceRequest $request, Application $application, ElectricDevice $device) {
		return $request->commit();
	}
	public function destroy(Application $application, ElectricDevice $device) {
		$device->delete();
		return [
			'success' => true
		];
	}
}
