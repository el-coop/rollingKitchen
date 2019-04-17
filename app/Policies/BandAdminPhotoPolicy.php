<?php

namespace App\Policies;

use App\Models\Developer;
use App\Models\User;
use App\Models\BandAdminPhoto;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandAdminPhotoPolicy {
	use HandlesAuthorization;

	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}
	/**
	 * Determine whether the user can view the band admin photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandAdminPhoto $bandAdminPhoto
	 * @return mixed
	 */
	public function view(User $user, BandAdminPhoto $bandAdminPhoto) {
		return $user->can('update', $bandAdminPhoto->bandAdmin->band);

	}

	/**
	 * Determine whether the user can create band admin photos.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		//
	}

	/**
	 * Determine whether the user can update the band admin photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandAdminPhoto $bandAdminPhoto
	 * @return mixed
	 */
	public function update(User $user, BandAdminPhoto $bandAdminPhoto) {
		//
	}

	/**
	 * Determine whether the user can delete the band admin photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandAdminPhoto $bandAdminPhoto
	 * @return mixed
	 */
	public function delete(User $user, BandAdminPhoto $bandAdminPhoto) {
		//
	}

	/**
	 * Determine whether the user can restore the band admin photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandAdminPhoto $bandAdminPhoto
	 * @return mixed
	 */
	public function restore(User $user, BandAdminPhoto $bandAdminPhoto) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the band admin photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandAdminPhoto $bandAdminPhoto
	 * @return mixed
	 */
	public function forceDelete(User $user, BandAdminPhoto $bandAdminPhoto) {
		//
	}
}
