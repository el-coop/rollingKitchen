<?php

namespace App\Http\Requests\Admin\Worker;

use Illuminate\Foundation\Http\FormRequest;

class DestroyWorkerRequest extends FormRequest {
	private $worker;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->worker = $this->route('worker');
		return $this->user()->can('delete', $this->worker);
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
	
	public function commit() {
		$this->worker->delete();
	}
}
