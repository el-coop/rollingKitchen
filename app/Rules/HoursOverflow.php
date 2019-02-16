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
		$this->maxHours = $this->calculateMaxHours($maxHours);
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed $value
	 * @return bool
	 */
	public function passes($attribute, $value) {
		$startTime = new Carbon($this->startTime);
		$endTime = new Carbon($value);
		if ($endTime <= $startTime) {
			$endTime->addDay();
		}
		$workHours = $startTime->diffAsCarbonInterval($endTime);
		$this->totalHours = $this->totalHours->add($workHours);
		return $this->totalHours->compare($this->maxHours) < 1;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message() {
		return __('admin/shifts.hoursOverflow');
	}

	private function calculateMaxHours($maxHoursInt) {
		$maxHours = new Carbon('today');
		$startOfDay = $maxHours->clone();
		$maxHours->addHours($maxHoursInt);
		return $maxHours->diffAsCarbonInterval($startOfDay);
	}
}
