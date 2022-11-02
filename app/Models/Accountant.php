<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accountant extends Model {
    use HasFactory;
	public function user() {
		return $this->morphOne(User::class, 'user');
	}

	public function homePage() {
		return action('HomeController@show');
	}
}
