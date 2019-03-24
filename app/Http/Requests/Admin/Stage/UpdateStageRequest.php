<?php

namespace App\Http\Requests\Admin\Stage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStageRequest extends FormRequest {
	private $stage;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->stage = $this->route('stage');
		return $this->user()->can('update', $this->stage);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|unique:stages,name,' . $this->stage->id,
		];
	}
	
	public function commit() {
		$this->stage->name = $this->input('name');
		$this->stage->save();
		
		return $this->stage;
	}
}
