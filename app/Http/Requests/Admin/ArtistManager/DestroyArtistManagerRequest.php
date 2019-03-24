<?php

namespace App\Http\Requests\Admin\ArtistManager;

use Illuminate\Foundation\Http\FormRequest;

class DestroyArtistManagerRequest extends FormRequest {
	private $artistManager;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->artistManager = $this->route('artistManager');
		return $this->user()->can('delete', $this->artistManager);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			//
		];
	}

	public function commit(){
		$this->artistManager->delete();
	}
}
