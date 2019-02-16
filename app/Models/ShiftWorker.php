<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ShiftWorker extends Pivot {
	
	public function workFunction() {
		return $this->belongsTo(WorkFunction::class);
	}
	
	public function worker() {
		return $this->belongsTo(Worker::class);
	}
	
	public function getWorkedHoursAttribute() {
		$endTime = new Carbon($this->end_time);
		$startTime = new Carbon($this->start_time);
		if ($endTime <= $startTime) {
			$endTime->addDay();
		}
		
		return $startTime->diffAsCarbonInterval($endTime);
	}
	
	public function getPaymentAttribute() {
		$function = $this->workFunction;
		if ($this->worker->type === 0) {
			$rate = $function->payment_per_hour_after_tax;
		} else {
			$rate = $function->payment_per_hour_before_tax;
		}
		
		return $this->workedHours->total('hours') * $rate;
	}
}
