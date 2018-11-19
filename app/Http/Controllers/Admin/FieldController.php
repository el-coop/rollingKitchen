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
		return view('admin.fields', compact('fields','class','type','indexLink'));
	}
	
	public function create(CreateFieldRequest $request) {
		$request->commit();
		return back();
	}
	
	public function edit(Field $field, EditFieldRequest $request) {
		$request->commit();
		return back();
	}
	
	public function delete(Field $field, DeleteFieldRequest $request) {
		$request->commit();
		return back();
	}
	
	public function saveOrder(OrderFieldRequest $request) {
		$request->commit();
		return back();
	}
}
