<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Error extends Model {

	public function error() {
		return $this->morphTo();
	}
}
