<?php

namespace App\Http\Requests\Admin\Kitchen;

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
			'status' => 'required|in:new,motherlist',
			'name' => 'required|min:2',
			'email' => 'required|email',
			'kitchen' => 'required|array',
			'kitchen.*' => 'required',
		];
	}
	
	public function commit() {
		
		$this->kitchen->user->name = $this->input('name');
		$this->kitchen->user->email = $this->input('email');
		$this->kitchen->status = $this->input('status');
		
		$this->kitchen->data = $this->input('kitchen');
		
		$this->kitchen->user->save();
		$this->kitchen->save();
		
		return [
			'id' => $this->kitchen->id,
			'name' => $this->input('name'),
			'email' => $this->input('email'),
			'status' => $this->input('status')
		];
	}
}
