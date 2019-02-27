<?php

namespace App\Http\Requests\Admin\Band;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBandRequest extends FormRequest {

	protected $band;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		return $this->user()->can('update', $this->band);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $this->band->user->id,
			'language' => 'required|in:en,nl',
			'band' => 'required|array',
		];
	}

	public function commit(){
		$this->band->user->name = $this->input('name');
		$this->band->user->email = $this->input('email');
		$this->band->user->language = $this->input('language');

		$this->band->data = array_filter($this->input('band'));
		$this->band->save();
		$this->band->user->save();

		return [
			'id' => $this->band->id,
			'name' => $this->input('name'),
			'email' => $this->input('email')
		];
	}
}
