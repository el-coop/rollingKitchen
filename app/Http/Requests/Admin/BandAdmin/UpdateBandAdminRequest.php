<?php

namespace App\Http\Requests\Admin\BandAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBandAdminRequest extends FormRequest {
	protected $bandAdmin;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandAdmin = $this->route('bandAdmin');
		return $this->user()->can('update', $this->bandAdmin->band);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$maxPayment = $this->bandAdmin->band->available_budget + $this->bandAdmin->payment;
		if ($maxPayment < 0) {
			$maxPayment = 0;
		}
		return [
			'name' => 'required|string',
			'bandAdmin' => 'required|array',
			'payment' => 'required|numeric|min:0|max:' . $maxPayment,
		];
	}

	public function commit() {
		$this->bandAdmin->name = $this->input('name');
		$this->bandAdmin->payment = $this->input('payment');
		$this->bandAdmin->data = array_filter($this->input('bandAdmin'));
		$this->bandAdmin->save();
	}
}
