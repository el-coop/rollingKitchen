<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Service\CreateServiceRequest;
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

    public function export(Excel $excel, KitchenServicesService $kitchenServicesService){
        return $excel->download($kitchenServicesService, __('admin/services.kitchenServices') . '.xls');
    }

}
