<?php

namespace App\Http\Requests\Worker\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class CloseShiftRequest extends FormRequest {
	protected $shift;
	protected $workplace;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->shift = $this->route('shift');
		$this->workplace = $this->route('workplace');
		return $this->user()->can('update', $this->shift) && !$this->shift->closed;
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
		$this->shift->closed = true;
		$this->shift->save();
		$fields = $this->shift->fullData->map(function ($value) {
			$value['readonly'] = true;
			return $value;
		});
		$shiftWorkers = $this->shift->workers->map(function ($worker) {
			$shift = $worker->shifts->find($this->shift);
			return [
				'id' => $worker->id,
				'worker' => $worker->id,
				'startTime' => date('H:i', strtotime($shift->pivot->start_time)),
				'endTime' => date('H:i', strtotime($shift->pivot->end_time)),
				'workFunction' => $worker->pivot->work_function_id
			
			];
		});
		return [
			'shift' => $fields->toArray(),
			'workers' => $this->workplace->workers()->with('user')->get()->pluck('user.name', 'id'),
			'shiftWorkers' => $shiftWorkers,
			'workFunctions' => $this->workplace->workFunctions->pluck('name', 'id'),
			'newShift' => [
				'id' => $this->shift->id,
				'name' => $this->workplace->name,
				'hours' => $this->shift->hours,
				'closed' => true
			],
		];
	}
}
