<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest {
	private $payment;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->payment = $this->route('invoicePayment');
		return $this->user()->can('update', $this->payment);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'date' => 'required|date',
			'amount' => 'required|numeric'
		];
	}

	public function commit(){
		$this->payment->date = $this->input('date');
		$this->payment->amount = $this->input('amount');
		$this->payment->save();
		return $this->payment;
	}
}
