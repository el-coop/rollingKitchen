<?php

namespace App\Http\Requests\Worker\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerShiftRequest extends FormRequest {
	protected $shift;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->shift = $this->route('shift');
		return $this->user()->can('update', $this->shift);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|integer',
			'start-time' => 'required|date_format:H:i',
			'end-time' => 'required|date_format:H:i'
		];
	}

	public function commit() {
		$this->shift->workers()->updateExistingPivot($this->input('name'), [
			'start_time' => $this->input('start-time'),
			'end_time' => $this->input('end-time')
		]);
		return [
			'id' => $this->input('name'),
			'name' => $this->input('name'),
			'start-time' => $this->input('start-time'),
			'end-time' => $this->input('end-time')
		];
	}

}
