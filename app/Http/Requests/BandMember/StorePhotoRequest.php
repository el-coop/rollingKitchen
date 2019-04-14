<?php

namespace App\Http\Requests\BandMember;

use App\Models\BandMemberPhoto;

class StorePhotoRequest extends \App\Http\Requests\Worker\StorePhotoRequest {
	/**
	 * @var \Illuminate\Routing\Route|object|string
	 */
	private $bandMember;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandMember = $this->route('bandMember');
		return $this->user()->can('update', $this->bandMember);
	}
	
	public function commit() {
		$path = $this->processPhoto();
		$photo = new BandMemberPhoto();
		$photo->file = basename($path);
		$this->bandMember->photos()->save($photo);
		
		return $photo;
	}
}
