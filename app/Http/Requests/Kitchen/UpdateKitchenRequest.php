<?php

namespace App\Http\Requests\Kitchen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKitchenRequest extends FormRequest {
	private $kitchen;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->kitchen = $this->route('kitchen');
		return $this->user()->can('update', $this->kitchen);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|min:2',
			'email' => 'required|email',
			'kitchen' => 'required|array',
			'application' => 'required|array',
			'services' => 'array',
			'socket' => 'required|numeric',
		];
	}
	
	public function commit() {
		$this->kitchen->user->name = $this->input('name');
		$this->kitchen->user->email = $this->input('email');
		$this->kitchen->user->save();
		
		
		$this->kitchen->data = $this->input('kitchen');
		$this->kitchen->save();
		
		$application = $this->kitchen->getCurrentApplication();
		$application->data = $this->input('application');
		$application->socket = $this->input('socket');
		$application->save();
		
		$application->services()->sync(collect($this->input('services'))->keys());
	}
}
