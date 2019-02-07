<?php

namespace App\Http\Requests\Worker\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class CloseShiftRequest extends FormRequest {
	protected $shift;
	
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
		return [
			//
		];
	}
	
	public function commit() {
		$this->shift->closed = true;
		$this->shift->save();
		
		return [
			'id' => $this->shift->id,
			'name' => $this->shift->workplace->name,
			'hours' => $this->shift->hours,
			'closed' => true
		];
	}
}
