<?php

namespace App\Http\Requests\Admin\Kitchen;

use Illuminate\Foundation\Http\FormRequest;

class DeleteKitchenRequest extends FormRequest {
	protected $kitchen;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->kitchen = $this->route('kitchen');
		return $this->user()->can('delete', $this->kitchen);
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
		$this->kitchen->delete();
	}
}
