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
	
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
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
		if ($this->password !== '' || !in_array($this->user_type,[Worker::class, ArtistManager::class, Band::class, BandMember::class])) {
			$this->notify(new ResetPasswordNotification($token));
			return;
		}
		switch ($this->user_type) {
			case ArtistManager::class:
				$this->notify(new \App\Notifications\ArtistManager\UserCreated($token));
				break;
			case Band::class:
				$this->notify(new \App\Notifications\Band\UserCreated($token));
				break;
			case BandMember::class:
				$this->notify(new \App\Notifications\BandMember\UserCreated($token));
				break;
			default:
				$this->notify(new UserCreated($token));
				break;
		}
	}

}
