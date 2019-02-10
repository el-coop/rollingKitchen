<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ShiftWorker extends Pivot {
	public function getWorkedHoursAttribute() {
		$endTime = new Carbon($this->end_time);
		$startTime = new Carbon($this->start_time);
		if ($endTime <= $startTime) {
			$endTime->addDay();
		}
		
		return $startTime->diffAsCarbonInterval($endTime);
	}
}
