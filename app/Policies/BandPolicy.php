<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\Developer;
use App\Models\User;
use App\Models\Band;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandPolicy {
	use HandlesAuthorization;

	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}

	/**
	 * Determine whether the user can view the band.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Band $band
	 * @return mixed
	 */
	public function view(User $user, Band $band) {
		return $user->user == $band;
	}

	/**
	 * Determine whether the user can create bands.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class;
	}

	/**
	 * Determine whether the user can update the band.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Band $band
	 * @return mixed
	 */
	public function update(User $user, Band $band) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class || $user->user == $band;
	}

	/**
	 * Determine whether the user can delete the band.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Band $band
	 * @return mixed
	 */
	public function delete(User $user, Band $band) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class;
	}

	/**
	 * Determine whether the user can restore the band.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Band $band
	 * @return mixed
	 */
	public function restore(User $user, Band $band) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the band.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Band $band
	 * @return mixed
	 */
	public function forceDelete(User $user, Band $band) {
		//
	}
}
