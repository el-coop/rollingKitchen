<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\KitchenExportColumn\CreateKitchenExportColumnRequest;
use App\Http\Requests\Admin\KitchenExportColumn\DestroyKitchenExportColumnRequest;
use App\Http\Requests\Admin\KitchenExportColumn\OrderKitchenExportColumnRequest;
use App\Http\Requests\Admin\KitchenExportColumn\UpdateKitchenExportColumnRequest;
use App\Models\KitchenExportColumn;
use App\Services\KitchenService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Excel;

class KitchenExportColumnController extends Controller {

	public function show() {
		$options = KitchenExportColumn::options();
		$alreadySelected = KitchenExportColumn::orderBy('order')->get();
		$downloadAction = action('Admin\KitchenExportColumnController@export');
		$addAction = action('Admin\KitchenExportColumnController@create');
		$title = __('admin/kitchens.exportTitle');
		$btn =__('admin/kitchens.exportTitle');
		return view('admin.workedHours.show', compact('options', 'alreadySelected', 'downloadAction', 'addAction', 'title', 'btn'));
	}

	public function create(CreateKitchenExportColumnRequest $request) {
		return $request->commit();
	}

	public function update(UpdateKitchenExportColumnRequest $request, KitchenExportColumn $kitchenExportColumn) {
		return $request->commit();
	}

	public function destroy(DestroyKitchenExportColumnRequest $request, KitchenExportColumn $kitchenExportColumn) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function saveOrder(OrderKitchenExportColumnRequest $request) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function export(Excel $excel, KitchenService $kitchenService) {
		return $excel->download($kitchenService, 'kitchens.xls');
	}
}
