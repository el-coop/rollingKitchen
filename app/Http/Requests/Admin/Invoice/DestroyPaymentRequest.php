<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class DestroyPaymentRequest extends FormRequest {
	private $payment;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->payment = $this->route('invoicePayment');
		return $this->user()->can('delete', $this->payment);
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
		$this->payment->delete();
	}
}
