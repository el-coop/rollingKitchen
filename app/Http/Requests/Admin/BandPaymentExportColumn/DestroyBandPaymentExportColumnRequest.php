<?php

namespace App\Http\Requests\Admin\BandPaymentExportColumn;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBandPaymentExportColumnRequest extends FormRequest {
	protected $bandPaymentColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandPaymentColumn = $this->route('bandPaymentExportColumn');
		return $this->user()->can('delete',$this->bandPaymentColumn);
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
		return $this->bandPaymentColumn->delete();
	}
}
