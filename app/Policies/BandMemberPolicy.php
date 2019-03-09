<?php

namespace App\Policies;

use App\Models\Band;
use App\Models\Developer;
use App\Models\User;
use App\Models\BandMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandMemberPolicy {
	use HandlesAuthorization;
	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}

	/**
	 * Determine whether the user can view the band member.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMember $bandMember
	 * @return mixed
	 */
	public function view(User $user, BandMember $bandMember) {
		//
	}

	/**
	 * Determine whether the user can create band members.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Band::class;
	}

	/**
	 * Determine whether the user can update the band member.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMember $bandMember
	 * @return mixed
	 */
	public function update(User $user, BandMember $bandMember) {
		return $user->user_type == Band::class && $bandMember->band->id == $user->user->id;
	}

	/**
	 * Determine whether the user can delete the band member.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMember $bandMember
	 * @return mixed
	 */
	public function delete(User $user, BandMember $bandMember) {
		return $user->user_type == Band::class && $bandMember->band->id == $user->user->id;
	}

	/**
	 * Determine whether the user can restore the band member.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMember $bandMember
	 * @return mixed
	 */
	public function restore(User $user, BandMember $bandMember) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the band member.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMember $bandMember
	 * @return mixed
	 */
	public function forceDelete(User $user, BandMember $bandMember) {
		//
	}
}
