<?php

namespace App\Http\Requests\Admin\BandMember;

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

	public function rules() {
		return [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $this->bandMember->user->id,
			'language' => 'required|in:en,nl',
			'bandmember' => 'required|array'
		];
	}

	public function commit(){
		$this->bandMember->user->name = $this->input('name');
		$this->bandMember->user->email = $this->input('email');
		$this->bandMember->user->language = $this->input('language');
		$this->bandMember->data = array_filter($this->input('bandmember'));
		$this->bandMember->save();
		$this->bandMember->user->save();
		return [
			'id' => $this->bandMember->id,
			'name' => $this->input('name'),
			'email' => $this->input('email')
		];
	}
}
