<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BandPaymentExportColumn\CreateBandPaymentExportColumnRequest;
use App\Http\Requests\Admin\BandPaymentExportColumn\DestroybandPaymentExportColumnRequest;
use App\Http\Requests\Admin\BandPaymentExportColumn\OrderBandPaymentExportColumnRequest;
use App\Http\Requests\Admin\BandPaymentExportColumn\UpdateBandPaymentExportColumnRequest;
use App\Models\BandPaymentExportColumn;
use App\Services\BandPaymentService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Http\Controllers\Controller;

class BandPaymentExportColumnController extends Controller {

	public function show() {
		$options = BandPaymentExportColumn::options();
		$alreadySelected = BandPaymentExportColumn::orderBy('order')->get();
		$downloadAction = action('Admin\BandPaymentExportColumnController@export');
		$addAction = action('Admin\BandPaymentExportColumnController@create');
		$title = __('admin/bands.bandPayments');
		$btn = __('admin/bands.exportBandPayments');
		return view('admin.workedHours.show', compact('options', 'alreadySelected', 'downloadAction', 'addAction', 'title', 'btn'));
	}

	public function create(CreateBandPaymentExportColumnRequest $request) {
		return $request->commit();
	}

	public function update(UpdateBandPaymentExportColumnRequest $request, BandPaymentExportColumn $bandPaymentExportColumn) {
		return $request->commit();
	}

	public function destroy(DestroyBandPaymentExportColumnRequest $request, BandPaymentExportColumn $bandPaymentExportColumn) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function saveOrder(OrderBandPaymentExportColumnRequest $request) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function export(Excel $excel, BandPaymentService $bandPaymentService) {
		return $excel->download($bandPaymentService, 'bandPayments.xls');
	}
}
