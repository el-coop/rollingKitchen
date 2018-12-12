<?php

namespace App\Http\Requests\Admin\Debtor;

use App\Models\Debtor;
use Illuminate\Foundation\Http\FormRequest;

class CreateDebtorRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Debtor::class);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|min:2',
			'email' => 'required|email',
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
		$debtor = new Debtor;
		
		$debtor->name = $this->input('name');
		$debtor->email = $this->input('email');
		$debtor->language = $this->input('language');
		$debtor->data = $this->input('kitchen');
		
		$debtor->save();
		
		return $debtor;
	}
}
