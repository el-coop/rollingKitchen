<?php

namespace App\Http\Requests\Admin\Workplace;

use App\Models\Workplace;
use Illuminate\Foundation\Http\FormRequest;

class CreateWorkplaceRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Workplace::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|string|unique:workplaces'
		];
	}

	public function commit(){
		$workplace = new Workplace;
		$workplace->name = $this->input('name');
		$workplace->save();

		return [
			'id' => $workplace->id,
			'name' => $this->input('name'),
		];
	}
}
