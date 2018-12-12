<?php

namespace App\Http\Requests\Admin\Debtor;

use Illuminate\Foundation\Http\FormRequest;

class DestroyDebtorRequest extends FormRequest {
	private $debtor;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->debtor = $this->route('debtor');
		return $this->user()->can('delete', $this->debtor);
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
		$this->debtor->delete();
	}
}
