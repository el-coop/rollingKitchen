<?php

namespace App\Rules;

use App\Models\Worker;
use Illuminate\Contracts\Validation\Rule;

class WorkerApproved implements Rule {
	protected $worker;
	
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}
	
	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed $value
	 * @return bool
	 */
	public function passes($attribute, $value) {
		if (!$value) {
			return true;
		}
		$this->worker = Worker::findOrFail($value);
		return $this->worker->approved;
	}
	
	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message() {
		return __('worker/worker.workerNotApproved');
	}
}
