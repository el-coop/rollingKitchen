<?php

namespace App\Http\Requests\Worker\Supervisor;

use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;

class AddWorkerToShiftRequest extends FormRequest {
	protected $shift;
	protected $worker;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->shift = $this->route('shift');
		$this->worker = Worker::findOrFail($this->input('worker'));
		return $this->user()->can('update', $this->shift) && $this->shift->workplace->hasWorker($this->worker) && !$this->shift->closed;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'worker' => 'required|integer',
			'startTime' => 'required|date_format:H:i',
			'endTime' => 'required|date_format:H:i',
			'workFunction' => 'required|integer'
		];
	}
	
	public function commit() {
		$this->shift->workers()->attach($this->worker, ['start_time' => $this->input('startTime'), 'end_time' => $this->input('endTime'), 'work_function_id' => $this->input('workFunction')]);
		return [
			'id' => $this->worker->id,
			'worker' => $this->worker->id,
			'startTime' => $this->input('startTime'),
			'endTime' => $this->input('endTime'),
			'workFunction' => $this->input('workFunction')
		];
	}
}
