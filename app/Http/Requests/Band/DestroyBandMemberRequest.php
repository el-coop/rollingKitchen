<?php

namespace App\Http\Requests\Band;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBandMemberRequest extends FormRequest {
	private $bandMember;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandMember = $this->route('bandMember');
		return $this->user()->can('delete', $this->bandMember);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			//
		];
	}

	public function commit(){
		$this->bandMember->delete();
	}
}
