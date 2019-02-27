<?php

namespace App\Http\Controllers;

use App\Models\Workplace;
use App\Services\DatatableService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class DatatableController extends Controller {
	
	public function list(Request $request, DatatableService $datatableService) {
		$query = $datatableService->query();
		return $query->paginate($request->input('per_page'));
	}
	
	public function export(Excel $excel, Request $request, DatatableService $datatableService) {
		return $excel->download($datatableService, "{$request->input('name')}.xls");
	}
	
	public function supervisorList(Request $request, Workplace $workplace) {
		$datatableService = new DatatableService($request, $workplace->{$request->input('attribute')});
		return $this->list($request, $datatableService);
	}

	public function artistManagerList(Request $request){
		$dataTableService = new DatatableService($request);
		return $this->list($request,$dataTableService);
	}
	
}
