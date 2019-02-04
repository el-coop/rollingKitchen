<?php

namespace App\Http\Requests\Admin\Shift;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShiftRequest extends FormRequest {
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

			'date' => 'required|date',
			'workplace' => 'required|exists:workplaces,id',
			'hours' => 'required|integer|min:1',
		];
	}

	public function commit() {

		$this->shift->date = $this->input('date');
		$this->shift->hours = $this->input('hours');
		$this->shift->workplace_id = $this->input('workplace');

		$this->shift->save();

		return [

			'id' => $this->shift->id,
			'date' => $this->shift->date,
			'name' => $this->shift->workplace->name,
			'hours' => $this->shift->hours,
			'closed' => $this->shift->closed,

		];
	}
}
