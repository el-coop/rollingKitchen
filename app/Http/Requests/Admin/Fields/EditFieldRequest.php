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
			'type' => 'required|string|in:text,textarea,checkbox,date',
			'status' => 'required|string|in:protected,required,encrypted,none',
			'options' => 'required_if:type,checkbox|array',
			'placeholder_nl' => 'nullable|string',
			'placeholder_en' => 'nullable|string',
            'tooltip_en' => 'required_with:has_tooltip',
            'tooltip_nl' => 'required_with:has_tooltip',
            'condition_field' => 'nullable|string',
            'condition_value' => "required_unless:condition_field,null"
		];
	}

	public function commit() {
        $this->field->name_en = $this->input('name_en');
		$this->field->name_nl = $this->input('name_nl');
		$this->field->type = $this->input('type');
		$this->field->status = $this->input('status');
		$this->field->placeholder_nl = $this->input('placeholder_nl');
		$this->field->placeholder_en = $this->input('placeholder_en');
        $this->field->has_tooltip = $this->has('has_tooltip');
        $this->field->tooltip_nl = $this->input('tooltip_nl');
        $this->field->tooltip_en = $this->input('tooltip_en');
        $this->field->condition_field = $this->input('condition_field');
        $this->field->condition_value = $this->input('condition_value');
		if ($this->field->type == 'checkbox') {
			$this->field->options = $this->input('options');
		}
		$this->field->save();
		return $this->field;
	}
}
