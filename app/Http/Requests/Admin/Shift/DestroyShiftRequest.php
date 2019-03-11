<?php

namespace App\Http\Requests\Admin\Shift;

use Illuminate\Foundation\Http\FormRequest;

class DestroyShiftRequest extends FormRequest {
	private $shift;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		
		$this->shift = $this->route('shift');
		return $this->user()->can('delete', $this->shift);
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
		$this->shift->delete();
	}
}
