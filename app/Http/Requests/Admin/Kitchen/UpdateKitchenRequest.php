<?php

namespace App\Http\Requests\Admin\Kitchen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKitchenRequest extends FormRequest {
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
			'status' => 'required|in:new,motherlist',
			'name' => 'required|min:2',
			'email' => 'required|email',
		];
	}
	
	public function commit() {
		$kitchen = $this->route('kitchen');
		
		$kitchen->user->name = $this->input('name');
		$kitchen->user->email = $this->input('email');
		$kitchen->status = $this->input('status');
		
		$kitchen->data = $this->except(['name', 'email', 'status']);
		
		$kitchen->user->save();
		$kitchen->save();
		
		return [
			'id' => $kitchen->id,
			'name' => $this->input('name'),
			'email' => $this->input('email'),
			'status' => $this->input('status')
		];
	}
}
