<?php

namespace App\Http\Requests\Developer;

use App\Models\Error;
use Illuminate\Foundation\Http\FormRequest;

class ResolveErrorRequest extends FormRequest {
	protected $error;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->error = $this->route('error');
		return $this->user()->can('delete', $this->error);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			//
		];
	}

	public function commit(){
		$this->error->delete();
	}
}
