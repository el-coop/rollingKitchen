<?php

namespace App\Rules;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Validation\Rule;

class HoursOverflow implements Rule {
	protected $startTime;
	protected $totalHours;
	protected $maxHours;

	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct($startTime, $totalHours, $maxHours) {
		$this->startTime = $startTime;
		$this->totalHours = $totalHours;
		$this->maxHours = CarbonInterval::hour($maxHours);
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed $value
	 * @return bool
	 * @throws \Exception
	 */
	public function passes($attribute, $value) {
		$startTime = new Carbon($this->startTime);
		$endTime = new Carbon($value);
		if ($endTime <= $startTime) {
			$endTime->addDay();
		}
		$workHours = $startTime->diffAsCarbonInterval($endTime);
        $this->totalHours = $this->totalHours->add($workHours);
        return $this->maxHours->greaterThanOrEqualTo($this->totalHours);
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message() {
		return __('admin/shifts.hoursOverflow');
	}
}
