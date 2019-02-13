<?php

namespace App\Http\Requests\Admin\Workplace;

use Illuminate\Foundation\Http\FormRequest;

class DeleteWorkFunctionRequest extends FormRequest {

	protected $workFunction;
	protected $workplace;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workplace = $this->route('workplace');
		$this->workFunction = $this->route('workFunction');
		return $this->user()->can('update', $this->workplace) && $this->user()->can('delete', $this->workFunction);
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
		$this->workFunction->delete();
	}
}
