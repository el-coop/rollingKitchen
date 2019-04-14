<?php

namespace App\Policies;

use App\Models\BandMemberPhoto;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandMemberPhotoPolicy {
	use HandlesAuthorization;
	
	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}
	
	/**
	 * Determine whether the user can view the worker photo.
	 *
	 * @param \App\Models\User $user
	 * @param BandMemberPhoto $bandMemberPhoto
	 * @return mixed
	 */
	public function view(User $user, BandMemberPhoto $bandMemberPhoto) {
		return $user->can('update', $bandMemberPhoto->bandMember);
	}
}
