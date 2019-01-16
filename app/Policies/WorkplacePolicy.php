<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkplacePolicy {
	use HandlesAuthorization;


	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}

	/**
	 * Determine whether the user can view the workplace.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Workplace $workplace
	 * @return mixed
	 */
	public function view(User $user, Workplace $workplace) {
		//
	}

	/**
	 * Determine whether the user can create workplaces.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can update the workplace.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Workplace $workplace
	 * @return mixed
	 */
	public function update(User $user, Workplace $workplace) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can delete the workplace.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Workplace $workplace
	 * @return mixed
	 */
	public function delete(User $user, Workplace $workplace) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can restore the workplace.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Workplace $workplace
	 * @return mixed
	 */
	public function restore(User $user, Workplace $workplace) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the workplace.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Workplace $workplace
	 * @return mixed
	 */
	public function forceDelete(User $user, Workplace $workplace) {
		//
	}
}
