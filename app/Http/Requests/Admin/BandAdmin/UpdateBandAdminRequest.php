<?php

namespace App\Http\Requests\Admin\BandAdmin;

use Illuminate\Contracts\Validation\Validator;
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
			'adminName' => 'required|string',
			'bandmember' => 'required|array',
			'payment' => 'required|numeric|min:0|max:' . $maxPayment,
		];
	}

	protected function failedValidation(Validator $validator) {
		$this->session()->put('bandAdminError');
		parent::failedValidation($validator); // TODO: Change the autogenerated stub
	}

	public function commit() {
		$this->bandAdmin->name = $this->input('adminName');
		$this->bandAdmin->payment = $this->input('payment');
		$this->bandAdmin->data = $this->input('bandmember');
		$this->bandAdmin->save();
	}
}
