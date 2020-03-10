<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\ArtistManager;
use App\Models\BandAdmin;
use App\Models\Developer;
use App\Models\User;
use App\Models\Band;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandPolicy {
	use HandlesAuthorization;

	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the band.
	 *
	 * @param \App\Models\User $user
	 * @param \App\Models\Band $band
	 * @return mixed
	 */
	public function view(User $user, Band $band) {
		return $user->user_id == $band->id && $user->user_type == Band::class;
	}

	/**
	 * Determine whether the user can create bands.
	 *
	 * @param \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class;
	}

	/**
	 * Determine whether the user can update the band.
	 *
	 * @param \App\Models\User $user
	 * @param \App\Models\Band $band
	 * @return mixed
	 */
	public function update(User $user, Band $band) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class || ($user->user_type == Band::class && $user->user->id == $band->id);
	}

	/**
	 * Determine whether the user can delete the band.
	 *
	 * @param \App\Models\User $user
	 * @param \App\Models\Band $band
	 * @return mixed
	 */
	public function delete(User $user, Band $band) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class;
	}

	/**
	 * Determine whether the user can restore the band.
	 *
	 * @param \App\Models\User $user
	 * @param \App\Models\Band $band
	 * @return mixed
	 */
	public function restore(User $user, Band $band) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the band.
	 *
	 * @param \App\Models\User $user
	 * @param \App\Models\Band $band
	 * @return mixed
	 */
	public function forceDelete(User $user, Band $band) {
		//
	}

	public function schedule(User $user) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class;
	}

	public function approveSchedule(User $user, Band $band) {
		return $user->user_type == Band::class && $user->user_id == $band->id;
	}

	public function sendConfirmation(User $user) {
		return $user->user_type == Admin::class || $user->user_type == ArtistManager::class;
	}

	public function manageSongs(User $user, Band $band) {
		return $user->user_type == Admin::class || $user->user == $band;
	}

	public function adminBandPdf(User $user) {
		return $user->user_type == Admin::class || $user->user_type == Accountant::class;

	}
}
