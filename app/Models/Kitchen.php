<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model {
	public function user() {
		return $this->morphOne(User::class, 'user');
	}
	
	public function photos() {
		return $this->hasMany(Photo::class);
	}
}
