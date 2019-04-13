<?php

namespace App\Http\Requests\Band;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBandMemberRequest extends FormRequest {
	private $bandMember;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandMember = $this->route('bandMember');
		return $this->user()->can('update', $this->bandMember);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$maxPayment = $this->bandMember->band->available_budget + $this->bandMember->payment;
		if ($maxPayment < 0) {
			$maxPayment = 0;
		}
		return [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $this->bandMember->user->id,
			'language' => 'required|in:en,nl',
			'payment' => 'required|numeric|min:0|max:' . $maxPayment,
		];
	}
	
	public function commit() {
		$this->bandMember->user->name = $this->input('name');
		$this->bandMember->user->email = $this->input('email');
		$this->bandMember->user->language = $this->input('language');
		$this->bandMember->payment = $this->input('payment');
		
		
		$this->bandMember->user->save();
		$this->bandMember->save();
		return [
			'id' => $this->bandMember->id,
			'name' => $this->input('name'),
			'email' => $this->input('email'),
			'language' => $this->input('language'),
			'payment' => $this->input('payment')
		];
	}
}
