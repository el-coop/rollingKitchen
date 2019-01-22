<?php

namespace App\Http\Requests\Admin\Worker;

use App\Models\Worker;
use App\Models\Workplace;
use Illuminate\Foundation\Http\FormRequest;

class CreateWorkerRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Worker::class);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required',
			'email' => 'required|email|unique',
			'type' => 'required|in:0,1,2',
			'language' => 'required|in:en,nl',
			'supervisor' => 'boolean',
			'workplaces' => 'required|array',
			'workplaces.*' => 'required|in:' . WorkPlace::select('id')->get()->implode('id', ',')
		];
	}
	
	public function commit() {
	
	}
}
