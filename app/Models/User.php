<?php

namespace App\Models;

use App\Notifications\Worker\UserCreated;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;


class User extends Authenticatable implements HasLocalePreference {
	use Notifiable;

    use HasFactory;

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
        'checked_info' => 'boolean'
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
		if ($this->password !== '' || !in_array($this->user_type,[Worker::class, ArtistManager::class, Band::class, BandMember::class, Kitchen::class])) {
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
            case Kitchen::class:
                $this->notify(new \App\Notifications\Kitchen\UserCreated($token));
                break;
			default:
				$this->notify(new UserCreated($token));
				break;
		}
	}

	public function routeNotificationForNexmo($notification) {
		$field = Field::where('form', $this->user_type)->whereRaw("LOWER (name_en) LIKE '%phone number%'")->first();
		$number =$this->user->data[$field->id];
		if ($number[0] == '0'){
			if ($number[1] == '0'){
				$number = substr_replace($number, '+', 0,2);
			} else {
				$number = substr_replace($number, '+31', 0,1);
			}
		}
		return $number;
	}

}
