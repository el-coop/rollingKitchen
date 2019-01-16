<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\WorkFunction;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkFunctionPolicy {
	use HandlesAuthorization;

	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}

	/**
	 * Determine whether the user can view the work function.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkFunction $workFunction
	 * @return mixed
	 */
	public function view(User $user, WorkFunction $workFunction) {
		//
	}

	/**
	 * Determine whether the user can create work functions.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can update the work function.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkFunction $workFunction
	 * @return mixed
	 */
	public function update(User $user, WorkFunction $workFunction) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can delete the work function.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkFunction $workFunction
	 * @return mixed
	 */
	public function delete(User $user, WorkFunction $workFunction) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can restore the work function.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkFunction $workFunction
	 * @return mixed
	 */
	public function restore(User $user, WorkFunction $workFunction) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the work function.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkFunction $workFunction
	 * @return mixed
	 */
	public function forceDelete(User $user, WorkFunction $workFunction) {
		//
	}
}
