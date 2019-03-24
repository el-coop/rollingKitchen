<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BandSchedule extends Model {
	public function stage() {
		return $this->belongsTo(Stage::class);
	}
	
	public function band() {
		return $this->belongsTo(Band::class);
	}
	
	public function getDateTimeAttribute($value) {
		return Carbon::parse($value)->format('d/m/Y H:i');
	}
}
