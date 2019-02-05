<?php

namespace App\Http\Requests\Worker\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class RemoveWorkerFromShiftRequest extends FormRequest {
	protected $shift;
	protected $worker;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->shift = $this->route('shift');
		$this->worker = $this->route('worker');
		return $this->user()->can('update', $this->shift) && !$this->shift->closed;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [

		];
	}

	public function commit(){
		$this->shift->workers()->detach($this->worker);
	}
}
