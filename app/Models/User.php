<?php

namespace App\Models;

use App\Notifications\Worker\UserCreated;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JustBetter\PaginationWithHavings\PaginationWithHavings;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;


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
	
	public function sendPasswordResetNotification($token) {
		if ($this->password !== '' || !($this->user_type === Worker::class || $this->user_type === ArtistManager::class)) {
			$this->notify(new ResetPasswordNotification($token));
			return;
		}
		if ($this->user_type === ArtistManager::class){
			$this->notify(new \App\Notifications\ArtistManager\UserCreated($token));
		} else {
			$this->notify(new UserCreated($token));
		}
	}
	
}
