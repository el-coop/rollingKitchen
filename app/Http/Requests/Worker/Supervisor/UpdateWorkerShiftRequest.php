<?php

namespace App\Http\Requests\Worker\Supervisor;

use App\Rules\HoursOverflow;
use App\Rules\WorkerApproved;
use Carbon\Carbon;
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
		return $this->user()->can('update', $this->shift) && !$this->shift->closed;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$previousLength = $this->calculatePreviousShiftLength($this->route('shiftWorker'));
		
		return [
			'worker' => ['nullable', 'integer', new WorkerApproved],
			'startTime' => 'required|date_format:H:i',
			'endTime' => ['required', 'date_format:H:i', new HoursOverflow($this->input('startTime'), $this->shift->totalHours->sub($previousLength), $this->shift->hours)],
			'workFunction' => 'required|integer'
		];
	}
	
	public function commit() {
		$shiftWorker = $this->route('shiftWorker');
		
		$shiftWorker->worker_id = $this->input('worker');
		$shiftWorker->start_time = $this->input('startTime');
		$shiftWorker->end_time = $this->input('endTime');
		$shiftWorker->work_function_id = $this->input('workFunction');
		$shiftWorker->save();
		
		return [
			'id' => $shiftWorker->id,
			'worker' => $shiftWorker->worker_id,
			'startTime' => $this->input('startTime'),
			'endTime' => $this->input('endTime'),
			'workFunction' => $this->input('workFunction'),
			'hours' => $shiftWorker->WorkedHours->total('hours')
		];
	}
	
	protected function calculatePreviousShiftLength($previousShift) {
		$startTime = new Carbon($previousShift->start_time);
		$endTime = new Carbon($previousShift->end_time);
		if ($endTime <= $startTime) {
			$endTime->addDay();
		}
		return $startTime->diffAsCarbonInterval($endTime);
	}
	
}
