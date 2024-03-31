<?php

namespace App\Http\Requests\Admin\Fields;

use App\Models\Application;
use App\Models\Band;
use App\Models\BandMember;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFieldRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Field::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name_en' => 'required|string|' . Rule::unique('fields')->where('form', $this->input('form')),
			'type' => 'required|string|in:text,textarea,checkbox,date',
			'form' => 'required|string|in:' . Kitchen::class . ',' . Application::class . ',' . Worker::class . ',' . Band::class . ',' . BandMember::class,
			'options' => 'required_if:type,checkbox|array',
			'status' => 'required|string|in:protected,required,encrypted,none',
			'name_nl' => 'required|string|' . Rule::unique('fields')->where('form', $this->input('form')),
			'placeholder_nl' => 'nullable|string',
			'placeholder_en' => 'nullable|string',
            'tooltip_en' => 'required_with:has_tooltip',
            'tooltip_nl' => 'required_with:has_tooltip',
            'condition_field' => 'nullable|string',
            'condition_value' => "required_unless:condition_field,null"
		];
	}

	public function commit() {
		$field = new Field;
		$field->form = $this->input('form');
		$field->name_en = $this->input('name_en');
		$field->type = $this->input('type');
		$field->name_nl = $this->input('name_nl');
		$field->status = $this->input('status');
		$field->placeholder_nl = $this->input('placeholder_nl');
		$field->placeholder_en = $this->input('placeholder_en');
        $field->has_tooltip = $this->has('has_tooltip');
        $field->tooltip_nl = $this->input('tooltip_nl');
        $field->tooltip_en = $this->input('tooltip_en');
        $field->condition_field = $this->input('condition_field');
        $field->condition_value = $this->input('condition_value');
		switch ($this->input('form')) {
			case Kitchen::class:
				$field->order = Kitchen::getLastFieldOrder() + 1;
				break;
			case Application::class:
				$field->order = Application::getLastFieldOrder() + 1;
				break;
			case Worker::class:
				$field->order = Worker::getLastFieldOrder() + 1;
				break;
			case Band::class:
				$field->order = Band::getLastFieldOrder() + 1;
				break;
			case BandMember::class:
				$field->order = BandMember::getLastFieldOrder() + 1;
				break;
		}
		if ($field->type == 'checkbox') {
			$field->options = $this->input('options');
		}
		$field->save();
		return $field;
	}
}
