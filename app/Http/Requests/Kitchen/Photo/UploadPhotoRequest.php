<?php

namespace App\Http\Requests\Kitchen\Photo;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->kitchen = $this->route('kitchen');
		return $this->user()->can('update', $this->kitchen);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'photo' => 'required|image'
		];
	}
	
	public function commit() {
		$path = $this->file('photo')->store('public/photos');
	}
}
