<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model {
	public function kitchen() {
		return $this->belongsTo(Kitchen::class);
	}
}
