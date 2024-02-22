<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Fields\CreateFieldRequest;
use App\Http\Requests\Admin\Fields\DeleteFieldRequest;
use App\Http\Requests\Admin\Fields\EditFieldRequest;
use App\Http\Requests\Admin\Fields\OrderFieldRequest;
use App\Models\Field;

class FieldController extends Controller {

	public function index($type) {
		$class = "App\\Models\\{$type}";
		$indexLink = $class::indexPage();
		$fields = $class::fields();
        $conditionalOptions = $class::getConditionalOptions();
		return view('admin.fields', compact('fields', 'class', 'type', 'indexLink', 'conditionalOptions'));
	}

	public function create(CreateFieldRequest $request) {
		return $request->commit();
	}

	public function edit(Field $field, EditFieldRequest $request) {
		return $request->commit();
	}

	public function destroy(Field $field, DeleteFieldRequest $request) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function saveOrder(OrderFieldRequest $request) {
		$request->commit();
		return [
			'success' => true
		];
	}
}
