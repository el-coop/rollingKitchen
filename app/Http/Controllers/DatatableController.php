<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Models\Workplace;
use App\Services\DatatableService;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class DatatableController extends Controller {

    public function list(Request $request, DatatableService $datatableService) {
        $query = $datatableService->query();

        return DB::query()->fromSub($query, '_')->paginate($request->input('per_page'));
    }

    public function export(Excel $excel, Request $request, DatatableService $datatableService) {
        return $excel->download($datatableService, "{$request->input('name')}.xls");
    }

    public function supervisorList(Request $request, Workplace $workplace) {
        $datatableService = new DatatableService($request, $workplace->{$request->input('attribute')});
        return $this->list($request, $datatableService);
    }

    public function artistManagerList(Request $request) {
        $dataTableService = new DatatableService($request);
        return $this->list($request, $dataTableService);
    }

    public function bandMemberList(Request $request, Band $band) {
        $dataTableService = new DatatableService($request, $band->{$request->input('attribute')});
        return $this->list($request, $dataTableService);

    }

}
