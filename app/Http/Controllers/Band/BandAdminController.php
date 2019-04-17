<?php

namespace App\Http\Controllers\Band;

use App\Http\Requests\Admin\BandAdmin\UpdateBandAdminRequest;
use App\Http\Requests\Band\BandAdmin\StorePhotoRequest;
use App\Models\Band;
use App\Models\BandAdmin;
use App\Models\BandAdminPhoto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BandAdminController extends Controller {

	public function update(UpdateBandAdminRequest $request,Band $band, BandAdmin $bandAdmin) {
		$request->commit();
		return back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess', [], $request->input('language'))
		]);
	}

	public function storePhoto(Band $band, BandAdmin $bandAdmin, StorePhotoRequest $request) {
		return $request->commit();
	}

	public function destroyPhoto(Band $band, BandAdmin $bandAdmin, BandAdminPhoto $photo) {
		$photo->delete();
		return [
			'success' => true
		];
	}
}
