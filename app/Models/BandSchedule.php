<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandSchedule extends Model {
    use HasFactory;

    public function stage() {
		return $this->belongsTo(Stage::class);
	}

	public function band() {
		return $this->belongsTo(Band::class);
	}

	public function getDateTimeAttribute($value) {
		return Carbon::parse($value)->format('d/m/Y H:i');
	}

	public function getEndTimeAttribute($value) {
		return Carbon::parse($value)->format('H:i');
	}

	public function getEndDateTimeAttribute($value) {
		return Carbon::parse($this->attributes['end_time']);
	}
}
