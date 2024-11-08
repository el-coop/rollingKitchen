<?php

namespace App\Http\Requests\Kitchen;

use App\Models\Kitchen;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateKitchenRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|string|min:2|unique:users',
			'email' => 'required|string|email|unique:users',
			'password' => 'required|string|min:6|confirmed',
			'language' => 'required|in:en,nl'
		];
	}

	public function commit() {
		$user = new User;
		$kitchen = new Kitchen;

		$user->name = $this->input('name');
		$user->email = $this->input('email');
		$user->password = bcrypt($this->input('password'));
		$user->language = $this->input('language');
		$kitchen->status = 'new';
		$kitchen->data = [];

		$kitchen->save();
		$kitchen->user()->save($user);
		return $kitchen;
	}
}
