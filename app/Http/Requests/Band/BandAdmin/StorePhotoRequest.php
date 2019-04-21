<?php

namespace App\Http\Requests\Band\BandAdmin;


use App\Models\BandAdminPhoto;

class StorePhotoRequest extends \App\Http\Requests\Worker\StorePhotoRequest {

	private $bandAdmin;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandAdmin = $this->route('bandAdmin');
		return $this->user()->can('update', $this->bandAdmin->band);
	}


	public function commit() {
		$path = $this->processPhoto();
		$photo = new BandAdminPhoto();
		$photo->file = basename($path);
		$this->bandAdmin->photos()->save($photo);
		return $photo;
	}
}
