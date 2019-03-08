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
		return [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $this->bandMember->user->id,
			'language' => 'required|in:en,nl',
		];
	}

	public function commit(){
		$this->bandMember->user->name = $this->input('name');
		$this->bandMember->user->email = $this->input('email');
		$this->bandMember->user->language = $this->input('language');
		$this->bandMember->user->save();
		return [
			'id' => $this->bandMember->id,
			'name' => $this->input('name'),
			'email' => $this->input('email'),
			'language' => $this->input('language')
		];
	}
}
