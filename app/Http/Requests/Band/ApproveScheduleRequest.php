<?php

namespace App\Http\Requests\Band;

use Illuminate\Foundation\Http\FormRequest;

class ApproveScheduleRequest extends FormRequest {
	private $band;
	private $schedule;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		$this->schedule = $this->route('bandSchedule');
		return $this->user()->can('approveSchedule', $this->band);
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

	public function commit(){
		$this->schedule->approved = 'accepted';
		$this->schedule->save();
		return [
			'id' => $this->schedule->id,
			'stage' => $this->schedule->stage->name,
			'date_time' => $this->schedule->date_time,
			'payment' => $this->schedule->payment,
			'approved' => $this->schedule->approved
		];

	}
}
