<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class TogglePaymentStatusRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->invoice = $this->route('invoice');
		return $this->user()->can('update', $this->invoice);
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
		$this->invoice->paid = !$this->invoice->paid;
		$this->invoice->save();
		return $this->invoice;
		
	}
}
