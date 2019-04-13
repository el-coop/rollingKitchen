<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BandMemberExportColumn\CreateBandMemberExportColumnRequest;
use App\Http\Requests\Admin\BandMemberExportColumn\DestroyBandMemberExportColumnRequest;
use App\Http\Requests\Admin\BandMemberExportColumn\OrderBandMemberExportColumnRequest;
use App\Http\Requests\Admin\BandMemberExportColumn\UpdateBandMemberExportColumnRequest;
use App\Models\BandMemberExportColumn;
use App\Services\BandMemberService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Excel;

class BandMemberExportColumnController extends Controller {
	public function show() {
		$options = BandMemberExportColumn::options();
		$alreadySelected = BandMemberExportColumn::orderBy('order')->get();
		$downloadAction = action('Admin\BandMemberExportColumnController@export');
		$addAction = action('Admin\BandMemberExportColumnController@create');
		$title = __('admin/bandMembers.exportBandMembersTitle');
		$btn = __('admin/bandMembers.exportBandMembersTitle');
		return view('admin.workedHours.show', compact('options', 'alreadySelected', 'downloadAction', 'addAction', 'title', 'btn'));
	}

	public function create(CreateBandMemberExportColumnRequest $request) {
		return $request->commit();
	}

	public function update(UpdateBandMemberExportColumnRequest $request, BandMemberExportColumn $bandMemberExportColumn) {
		return $request->commit();
	}

	public function destroy(DestroyBandMemberExportColumnRequest $request, BandMemberExportColumn $bandMemberExportColumn) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function saveOrder(OrderBandMemberExportColumnRequest $request) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function export(Excel $excel, BandMemberService $bandMemberService) {
		return $excel->download($bandMemberService, 'BandMemberPayment.xls');
	}
}
