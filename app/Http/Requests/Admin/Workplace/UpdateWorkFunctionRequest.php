<?php

namespace App\Http\Requests\Admin\Workplace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkFunctionRequest extends FormRequest {

	protected $workFunction;
	protected $workplace;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workplace = $this->route('workplace');
		$this->workFunction = $this->route('workFunction');
		return $this->user()->can('update', $this->workplace) && $this->user()->can('update', $this->workFunction);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|string|' . Rule::unique('work_functions')->where('workplace_id', $this->workplace->id)->ignore($this->workFunction->id),
			'payment_per_hour_before_tax' => 'required|numeric',
			'payment_per_hour_after_tax' => 'required|numeric'
		];
	}

	public function commit(){
		$this->workFunction->name = $this->input('name');
		$this->workFunction->payment_per_hour_after_tax = $this->input('payment_per_hour_after_tax');
		$this->workFunction->payment_per_hour_before_tax = $this->input('payment_per_hour_before_tax');
		$this->workFunction->save();
		return $this->workFunction;
	}
}
