<?php

namespace App\Models;

use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JustBetter\PaginationWithHavings\PaginationWithHavings;

class User extends Authenticatable implements HasLocalePreference {
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
	
	/**
	 * Get the preferred locale of the entity.
	 *
	 * @return string|null
	 */
	public function preferredLocale() {
		return $this->language;
	}
}
