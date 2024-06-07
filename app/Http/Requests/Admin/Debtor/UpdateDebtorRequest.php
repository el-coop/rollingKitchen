<?php

namespace App\Http\Requests\Admin\Debtor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtorRequest extends FormRequest {
	private $debtor;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->debtor = $this->route('debtor');
		return $this->user()->can('update', $this->debtor);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|min:2|unique:debtors,name,' . $this->debtor->id,
			'email' => 'required|email|unique:debtors,email,' . $this->debtor->id,
			'language' => 'required|in:en,nl',
			'kitchen' => 'required|array',
			'kitchen.1' => 'required',
			'kitchen.2' => 'required',
			'kitchen.3' => 'required',
			'kitchen.4' => 'required',
			'kitchen.5' => 'required',
		];
	}

	public function commit() {
		$this->debtor->name = $this->input('name');
		$this->debtor->email = $this->input('email');
		$this->debtor->language = $this->input('language');
		$this->debtor->data = json_encode($this->input('kitchen'));

		$this->debtor->save();

		return $this->debtor;

	}
}
