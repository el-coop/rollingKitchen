<?php

namespace App\Http\Requests\Admin\Stage;

use Illuminate\Foundation\Http\FormRequest;

class DestroyStageRequest extends FormRequest {
	private $stage;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->stage = $this->route('stage');
		return $this->user()->can('delete', $this->stage);
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
	
	public function commit() {
		$this->stage->delete();
	}
}
