<?php

namespace App\Http\Requests\Admin\Stage;

use App\Models\Stage;
use Illuminate\Foundation\Http\FormRequest;

class CreateStageRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Stage::class);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|unique:stages'
		];
	}
	
	public function commit() {
		$stage = new Stage;
		$stage->name = $this->input('name');
		$stage->save();
		
		return $stage;
	}
}
