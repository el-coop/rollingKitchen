<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkFunction extends Model {

	public function workplace(){
		return $this->belongsTo(Workplace::class);
	}
}
