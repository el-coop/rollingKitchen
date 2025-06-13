<?php

namespace App\Http\Requests\Worker\Supervisor;

use App\Models\ShiftWorker;
use App\Models\Worker;
use App\Rules\HoursOverflow;
use App\Rules\WorkerApproved;
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
		$hasWorker = true;
		if ($this->input('worker')) {
			$this->worker = Worker::findOrFail($this->input('worker'));
			$hasWorker = $this->shift->workplace->hasWorker($this->worker);
		}
		return $this->user()->can('update', $this->shift) && $hasWorker && !$this->shift->closed;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
//			'worker' => ['nullable', 'integer', new WorkerApproved],
			'startTime' => 'required|date_format:H:i',
			'endTime' => ['required', 'date_format:H:i', new HoursOverflow($this->input('startTime'), $this->shift->totalHours, $this->shift->hours)],
			'workFunction' => 'required|integer'
		];
	}

	public function commit() {
		$shiftWorker = new ShiftWorker;
		$shiftWorker->worker_id = $this->worker->id ?? null;
		$shiftWorker->shift_id = $this->shift->id;
		$shiftWorker->start_time = $this->input('startTime');
		$shiftWorker->end_time = $this->input('endTime');
		$shiftWorker->work_function_id = $this->input('workFunction');
		$shiftWorker->save();

		return [
			'id' => $shiftWorker->id,
			'worker' => $this->worker->id ?? null,
			'startTime' => $this->input('startTime'),
			'endTime' => $this->input('endTime'),
			'workFunction' => $this->input('workFunction'),
			'hours' => $shiftWorker->WorkedHours->total('hours')
		];
	}
}
