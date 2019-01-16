<?php

namespace App\Http\Requests\Admin\Workplace;

use Illuminate\Foundation\Http\FormRequest;

class DeleteWorkplaceRequest extends FormRequest {

	protected  $workplace;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workplace = $this->route('workplace');
		return $this->user()->can('delete', $this->workplace);
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
		$this->workplace->delete();
	}
}
