<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JustBetter\PaginationWithHavings\PaginationWithHavings;

class User extends Authenticatable {
	use Notifiable;
	use PaginationWithHavings;
	
	
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];
	
	public function user() {
		return $this->morphTo();
	}
}
