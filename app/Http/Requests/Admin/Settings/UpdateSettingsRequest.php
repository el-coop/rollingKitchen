<?php

namespace App\Http\Requests\Admin\Settings;

use App\Models\Accountant;
use App\Models\User;
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

		$rules['accountant_email'] = 'required|email';
		unset($rules['general_registration_status']);
		unset($rules['accountant_password']);
		return $rules;
	}

	public function commit() {
		$accountant = User::where('user_type', Accountant::class)->first();
		if ($this->input('accountant_password') != '') {
			$accountant->password = bcrypt($this->input('accountant_password'));
		}
		$accountant->email = $this->input('accountant_email');
		$accountant->save();
		foreach ($this->fields as $field) {
			switch ($field) {
				case 'general_registration_status':
					$value = $this->filled($field);
					break;
				case 'accountant_password':
					$value = '';
					break;
				default:
					$value = $this->input($field);
					break;
			}
			$this->settings->put($field, $value);
		}
	}
}
