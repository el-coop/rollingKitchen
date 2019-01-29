<?php

namespace App\Http\Requests\Admin\Shift;

use App\Models\Shift;
use Illuminate\Foundation\Http\FormRequest;

class CreateShiftRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Shift::class);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'date' => 'required|date',
			'workplace' => 'required|exists:workplaces,id',
			'hours' => 'required|integer|min:1'
		];
	}
	
	public function commit() {
		$shift = new Shift;
		
		$shift->date = $this->input('date');
		$shift->hours = $this->input('hours');
		$shift->workplace_id = $this->input('workplace');
		$shift->save();
		$shift->name = $shift->workplace->name;
		return $shift;
	}
}
