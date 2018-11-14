<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model {

    use HasFields;

	public function user() {
		return $this->morphOne(User::class, 'user');
	}

	public function photos() {
		return $this->hasMany(Photo::class);
	}
}
