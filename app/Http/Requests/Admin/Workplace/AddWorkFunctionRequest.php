<?php

namespace App\Http\Requests\Admin\Workplace;

use App\Models\WorkFunction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddWorkFunctionRequest extends FormRequest {

	protected $workplace;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workplace = $this->route('workplace');
		return $this->user()->can('create', WorkFunction::class) && $this->user()->can('update', $this->workplace);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|string|' . Rule::unique('work_functions')->where('workplace_id', $this->workplace->id),
			'payment_per_hour_before_tax' => 'required|numeric',
			'payment_per_hour_after_tax' => 'required|numeric'
		];
	}

	public function commit(){
		$workFunction = new WorkFunction;
		$workFunction->name = $this->input('name');
		$workFunction->payment_per_hour_after_tax = $this->input('payment_per_hour_after_tax');
		$workFunction->payment_per_hour_before_tax = $this->input('payment_per_hour_before_tax');
		$this->workplace->workFunctions()->save($workFunction);
		return $workFunction;
	}
}
