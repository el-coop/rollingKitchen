<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Band\CreateBandRequest;
use App\Http\Requests\Admin\Band\DestroyBandRequest;
use App\Http\Requests\Admin\Band\UpdateBandRequest;
use App\Models\Band;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BandController extends Controller {

	public function index() {
		$title = __('admin/artists.bands');
		$fieldType = "Band";
		$createTitle = __('admin/artists.createBand');
		$withEditLink = false;
		return view('admin.datatableWithNew', compact('title', 'createTitle', 'fieldType', 'withEditLink'));
	}

	public function create(){
		return (new Band)->fullData;
	}

	public function store(CreateBandRequest $request){
		return $request->commit();
	}

	public function edit(Band $band){
		return $band->fullData;
	}

	public function update(UpdateBandRequest $request, Band $band){
		return $request->commit();
	}

	public function destroy(DestroyBandRequest $request, Band $band){
		$request->commit();

		return [
			'success' => true
		];
	}
}
