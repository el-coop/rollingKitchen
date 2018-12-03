<?php

namespace App\Http\Requests\Admin\Settings;

use Dotenv\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UpdateSettingsRequest extends FormRequest {
	private $fields;
	private $settings;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return Gate::allows('update-settings');
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$this->settings = app('settings');
		$this->fields = array_keys($this->settings->all());
		
		$rules = [];
		foreach ($this->fields as $field) {
			$rules[$field] = 'required|string';
		};
		
		$rules['invoices_accountant'] = 'required|email';
		unset($rules['general_registration_status']);
		return $rules;
	}
	
	public function commit() {
		foreach ($this->fields as $field) {
			if ($field === 'general_registration_status') {
				$value = $this->filled($field);
			} else {
				$value = $this->input($field);
			}
			$this->settings->put($field, $value);
		}
	}
}
