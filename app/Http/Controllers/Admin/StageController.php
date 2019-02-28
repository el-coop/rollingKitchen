<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Stage\CreateStageRequest;
use App\Http\Requests\Admin\Stage\DestroyStageRequest;
use App\Http\Requests\Admin\Stage\UpdateStageRequest;
use App\Models\Stage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StageController extends Controller {
	
	public function index() {
		$title = __('admin/artists.stages');
		$createTitle = __('admin/artists.createStage');
		$withEditLink = false;
		
		
		return view('admin.datatableWithNew', compact('title', 'createTitle', 'withEditLink'));
	}
	
	public function create() {
		return (new Stage)->fullData;
	}
	
	public function store(CreateStageRequest $request) {
		return $request->commit();
	}
	
	public function edit(Stage $stage) {
		return $stage->fullData;
	}
	
	public function update(UpdateStageRequest $request, Stage $stage) {
		return $request->commit();
	}
	
	public function destroy(DestroyStageRequest $request, Stage $stage) {
		$request->commit();
		
		return [
			'success' => true
		];
	}
}
