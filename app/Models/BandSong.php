<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BandSong extends Model {
	
	public function band() {
		return $this->belongsTo(Band::class);
	}
}
