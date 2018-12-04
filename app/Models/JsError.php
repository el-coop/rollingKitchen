<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JsError extends Model {
	protected $casts = [
		'vm' => 'array'
	];
	public function error() {
		return $this->morphOne(Error::class, 'error');
	}
}
