<?php

namespace App\Http\Requests\Admin\Settings;

use Dotenv\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UpdateSettingsRequest extends FormRequest {
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
		return [
			'accountant' => 'required|email',
			'application_text_en' => 'required|string',
			'application_text_nl' => 'required|string',
			'registration_text_en' => 'required|string',
			'registration_text_nl' => 'required|string',
			'login_text_en' => 'required|string',
			'login_text_nl' => 'required|string'
		];
	}

	public function commit() {
	    $settings = app('settings');
		$names = array_keys($settings->all());
		foreach ($names as $name) {
			if ($name === 'registration_status') {
				$value = $this->has($name);
			} else {
				$value = $this->input($name);
			}
			$settings->put($name,$value);
		}
	}
}
