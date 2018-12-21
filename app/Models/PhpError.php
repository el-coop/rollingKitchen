<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhpError extends Model {
	protected $casts = [
		'request' => 'array',
		'exception' => 'array',
	];
	
	public function error() {
		return $this->morphOne(Error::class, 'error');
	}
}
