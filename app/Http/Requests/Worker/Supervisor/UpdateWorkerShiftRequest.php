<?php

namespace App\Http\Requests\Worker\Supervisor;

use App\Rules\HoursOverflow;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerShiftRequest extends FormRequest {
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
			'startTime' => 'required|date_format:H:i',
			'endTime' => ['required','date_format:H:i', new HoursOverflow($this->input('startTime'), $this->shift->totalHours, $this->shift->hours)],
			'workFunction' => 'required|integer'
		];
	}

	public function commit() {
		$this->shift->workers()->updateExistingPivot($this->worker, [
			'start_time' => $this->input('startTime'),
			'end_time' => $this->input('endTime'),
			'work_function_id' => $this->input('workFunction')
		]);
		return [
			'id' => $this->worker->id,
			'worker' => $this->worker->id,
			'startTime' => $this->input('startTime'),
			'endTime' => $this->input('endTime'),
			'workFunction' => $this->input('workFunction')
		];
	}

}
