<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\WorkedHoursExportColumn\CreateWorkedHoursExportColumnRequest;
use App\Http\Requests\Admin\WorkedHoursExportColumn\DestroyWorkedHoursExportColumnRequest;
use App\Http\Requests\Admin\WorkedHoursExportColumn\OrderWorkedHoursExportColumnRequest;
use App\Http\Requests\Admin\WorkedHoursExportColumn\UpdateWorkedHoursExportColumnRequest;
use App\Models\WorkedHoursExportColumn;
use App\Services\WorkedHoursService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Excel;

class WorkedHoursExportColumnController extends Controller {
    
    public function show() {
        $options = WorkedHoursExportColumn::options();
        $alreadySelected = WorkedHoursExportColumn::orderBy('order')->get();
        $downloadAction = action('Admin\WorkedHoursExportColumnController@export');
        $addAction = action('Admin\WorkedHoursExportColumnController@create');
        $title = __('admin/settings.workedHours');
        $btn = __('admin/shifts.exportWorkedHours');
        $withDate = true;
        return view('admin.workedHours.show', compact('alreadySelected', 'options', 'downloadAction', 'addAction', 'title', 'btn', 'withDate'));
    }
    
    public function create(CreateWorkedHoursExportColumnRequest $request) {
        return $request->commit();
    }
    
    public function update(UpdateWorkedHoursExportColumnRequest $request, WorkedHoursExportColumn $workedHoursExportColumn) {
        return $request->commit();
    }
    
    public function destroy(DestroyWorkedHoursExportColumnRequest $request, WorkedHoursExportColumn $workedHoursExportColumn) {
        $request->commit();
        return [
            'success' => true
        ];
    }
    
    public function saveOrder(OrderWorkedHoursExportColumnRequest $request) {
        $request->commit();
        return [
            'success' => true
        ];
    }
    
    public function export(Excel $excel, WorkedHoursService $workedHoursService) {
        return $excel->download($workedHoursService, 'workedHours.xls');
    }
}
