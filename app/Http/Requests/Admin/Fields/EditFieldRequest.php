<?php

namespace App\Http\Requests\Admin\Fields;

use App\Models\Kitchen;
use Illuminate\Foundation\Http\FormRequest;

class EditFieldRequest extends FormRequest {
	protected $field;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->field = $this->route('field');
		return true;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|string',
			'type' => 'required|string|in:text,textarea,checkbox',
			'options' => 'required_if:type,checkbox|array'
		];
	}
	
	public function commit() {
		$this->field->name = $this->input('name');
		$this->field->type = $this->input('type');
		if ($this->field->type == 'checkbox') {
			$this->field->options = $this->input('options');
		}
		$this->field->save();
		return $this->field;
	}
}
