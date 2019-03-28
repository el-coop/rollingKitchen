<?php

namespace App\Http\Requests\ArtistManager;

use App\Models\ArtistManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateConfrimationEmailRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return Gate::allows('update-confirmation-email');
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'subject_en' => 'required|string',
			'subject_nl' => 'required|string',
			'text_en' => 'required|string',
			'text_nl' => 'required|string',
		];
	}

	public function commit(){
		$settings = app('settings');
		$settings->put('bands_confirmation_subject_en', $this->input('subject_en'));
		$settings->put('bands_confirmation_subject_nl', $this->input('subject_nl'));
		$settings->put('bands_confirmation_text_en', $this->input('text_en'));
		$settings->put('bands_confirmation_text_en', $this->input('text_en'));
	}
}
