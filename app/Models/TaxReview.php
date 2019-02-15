<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxReview extends Model {
	public function worker() {
		return $this->belongsTo(Worker::class);
	}
}
