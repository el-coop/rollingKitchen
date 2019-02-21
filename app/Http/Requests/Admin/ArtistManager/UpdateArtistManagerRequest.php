<?php

namespace App\Http\Requests\Admin\ArtistManager;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArtistManagerRequest extends FormRequest {
	private $artistManager;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->artistManager = $this->route('artistManager');
		return $this->user()->can('update', $this->artistManager);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $this->artistManager->user->id,
			'language' => 'required|in:en,nl',
		];
	}

	public function commit(){
		$this->artistManager->user->name = $this->input('name');
		$this->artistManager->user->email = $this->input('email');
		$this->artistManager->user->language = $this->input('language');
		$this->artistManager->user->save();
		return [
			'id' => $this->artistManager->id,
			'name' => $this->input('name'),
			'email' => $this->input('email'),
		];
	}
}
