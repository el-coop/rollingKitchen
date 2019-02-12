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
		$this->updateAccountant();
		$this->fields = array_diff($this->fields, ['accountant_password']);
		foreach ($this->fields as $field) {
			if ($field == 'general_registration_status') {
				$value = $this->filled($field);
			} else {
				$value = $this->input($field);
			}
			$this->settings->put($field, $value);
		}
	}
	
	private function updateAccountant(): void {
		$accountant = User::where(['user_type' => Accountant::class])->first();
		if (!$accountant) {
			$accountant = new User;
			$accountantUser = new Accountant;
			$accountant->name = 'Accountant';
		}
		if ($this->input('accountant_password') != '') {
			$accountant->password = bcrypt($this->input('accountant_password'));
		}
		$accountant->email = $this->input('accountant_email');
		if (isset($accountantUser)) {
			$accountantUser->save();
			$accountantUser->user()->save($accountant);
		} else {
			$accountant->save();
		}
	}
}
