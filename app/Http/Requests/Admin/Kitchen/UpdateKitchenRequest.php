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
			'name' => 'required|min:2|unique:users,name,' . $this->kitchen->user->id,
			'language' => 'required|in:en,nl',
			'email' => 'required|email|unique:users,email,' . $this->kitchen->user->id,
			'kitchen' => 'required|array'
		];
	}

	public function commit() {
		$this->kitchen->user->name = $this->input('name');
		$this->kitchen->user->email = $this->input('email');
		$this->kitchen->user->language = $this->input('language');
		$this->kitchen->status = $this->input('status');
        $this->kitchen->note = $this->note;

        $this->kitchen->data = json_encode($this->input('kitchen'));

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
