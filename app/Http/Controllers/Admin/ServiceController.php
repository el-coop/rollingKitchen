<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Service\CreateServiceRequest;
use App\Http\Requests\Admin\Service\DestroyServiceRequest;
use App\Http\Requests\Admin\Service\UpdateServiceRequest;
use App\Models\Kitchen;
use App\Models\Service;
use App\Models\User;
use App\Services\KitchenServicesService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Excel;

class ServiceController extends Controller {
	use Exportable;
	
	public function index() {
		$buttons = ['<a href="' . action('Admin\ServiceController@export') . '" class="button is-info">' . __('admin/services.download_application_services') . '</a>'];
		
		$title = __('kitchen/kitchen.services');
		$createTitle = __('admin/services.createService');
		$withEditLink = false;
		
		return view('admin.datatableWithNew', compact('title', 'createTitle', 'buttons', 'withEditLink'));
		
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
	
	public function export(Excel $excel, KitchenServicesService $kitchenServicesService) {
		return $excel->download($kitchenServicesService, __('admin/services.kitchenServices') . '.xls');
	}
	
	public function destroy(DestroyServiceRequest $request, Service $service) {
		$request->commit();
	}
	
}
