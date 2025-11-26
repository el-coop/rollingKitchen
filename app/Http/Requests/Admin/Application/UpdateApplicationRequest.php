<?php

namespace App\Http\Requests\Admin\Application;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest {
	protected $application;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->application = $this->route('application');
		return $this->user()->can('update', $this->application);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
        $applicationRule = Application::fields()->isEmpty() ? 'array' : 'required|array';
        $maxYear = max(date('Y'), app('settings')->get('registration_year'));
		return [
			'status' => 'required|in:accepted,pending,rejected,reopened,backup',
			'year' => 'digits:4|integer|min:2014|max:' . $maxYear,
			'application' => $applicationRule
		];
	}

	public function commit() {
		$this->application->status = $this->input('status');
		$this->application->year = $this->input('year', $this->application->year);
		$this->application->data = $this->input('application');

		$this->application->save();


		return [
			'id' => $this->application->id,
			'year' => $this->input('year', $this->application->year),
			'status' => $this->input('status')
		];
	}
}
