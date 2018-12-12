<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\InvoicePayment;
use Illuminate\Foundation\Http\FormRequest;

class AddPaymentRequest extends FormRequest {
	private $invoice;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->invoice = $this->route('invoice');
		return $this->user()->can('create', InvoicePayment::class);
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
		$payment = new InvoicePayment;
		$payment->amount = $this->input('amount');
		$payment->date = $this->input('date');
		$this->invoice->payments()->save($payment);
		return $payment;
	}
}
