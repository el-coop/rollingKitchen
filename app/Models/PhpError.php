<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhpError extends Model {
	public function error(){
		return $this->morphOne(Error::class, 'error');
	}
}
