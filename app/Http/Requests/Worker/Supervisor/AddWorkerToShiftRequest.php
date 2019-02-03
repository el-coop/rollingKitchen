<?php

namespace App\Http\Requests\Worker\Supervisor;

use App\Models\Shift;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;

class AddWorkerToShiftRequest extends FormRequest {
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
		return $this->user()->can('update', $this->shift) && $this->workplace->hasWorker(Worker::find($this->input('name')));
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

	public  function commit(){
		$worker =  Worker::find($this->input('name'));
		$this->shift->workers()->attach($worker, ['start_time' => $this->input('start-time'), 'end_time' => $this->input('end-time')]);
		return [
			'id' => $worker->id,
			'name' =>  $worker->id,
			'start-time' => $this->input('start-time'),
			'end-time' => $this->input('end-time')
		];
	}
}
