<?php

namespace App\Http\Requests\Admin\Band;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBandRequest extends FormRequest {

	protected $band;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		return $this->user()->can('delete', $this->band) ;
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
		$this->band->delete();
	}
}
