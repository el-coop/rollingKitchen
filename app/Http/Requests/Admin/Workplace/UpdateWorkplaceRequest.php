<?php

namespace App\Http\Requests\Admin\Workplace;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkplaceRequest extends FormRequest {

	protected $workplace;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workplace = $this->route('workplace');
		return $this->user()->can('update', $this->workplace);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|string|unique:workplaces,name,' . $this->workplace->id
		];
	}

	public function commit(){
		$this->workplace->name = $this->input('name');
		$this->workplace->save();

		return [
			'id' => $this->workplace->id,
			'name' => $this->input('name'),
		];
	}
}
