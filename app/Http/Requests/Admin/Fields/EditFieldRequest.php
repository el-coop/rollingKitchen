<?php

namespace App\Http\Requests\Admin\Fields;

use App\Models\Kitchen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditFieldRequest extends FormRequest {
	protected $field;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->field = $this->route('field');
		return $this->user()->can('update', $this->field);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name_en' => 'required|string|' . Rule::unique('fields')->where('form', $this->field->form)->ignore($this->field->id),
			'name_nl' => 'required|string|' . Rule::unique('fields')->where('form', $this->field->form)->ignore($this->field->id),
			'type' => 'required|string|in:text,textarea,checkbox',
			'status' => 'required|string|in:protected,required,encrypted,none',
			'options' => 'required_if:type,checkbox|array',
			'placeholder_nl' => 'nullable|string',
			'placeholder_en' => 'nullable|string'
		];
	}
	
	public function commit() {
		$this->field->name_en = $this->input('name_en');
		$this->field->name_nl = $this->input('name_nl');
		$this->field->type = $this->input('type');
		$this->field->status = $this->input('status');
		$this->field->placeholder_nl = $this->input('placeholder_nl');
		$this->field->placeholder_en = $this->input('placeholder_en');
		if ($this->field->type == 'checkbox') {
			$this->field->options = $this->input('options');
		}
		$this->field->save();
		return $this->field;
	}
}
