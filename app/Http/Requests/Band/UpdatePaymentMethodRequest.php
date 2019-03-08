<?php

namespace App\Http\Requests\Band;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest {
	protected $band;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		return $this->user()->can('update', $this->band);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'paymentMethod' => 'required|string|in:band,individual'
		];
	}

	public function commit() {
		$this->band->payment_method = $this->input('paymentMethod');
		$this->band->save();
	}
}
