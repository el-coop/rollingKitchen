<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\Shift;
use App\Models\Worker;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShiftPolicy {
	use HandlesAuthorization;
	
	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}
	
	/**
	 * Determine whether the user can view the shift.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Shift $shift
	 *
	 * @return mixed
	 */
	public function view(User $user, Shift $shift) {
		//
	}
	
	/**
	 * Determine whether the user can create shifts.
	 *
	 * @param  \App\Models\User $user
	 *
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;
	}
	
	/**
	 * Determine whether the user can update the shift.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Shift $shift
	 *
	 * @return mixed
	 */
	public function update(User $user, Shift $shift) {
		return $user->user_type == Admin::class || ($user->user_type == Worker::class && $user->user->isSupervisor() && $shift->workplace->hasWorker($user->user));
	}
	
	/**
	 * Determine whether the user can delete the shift.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Shift $shift
	 *
	 * @return mixed
	 */
	public function delete(User $user, Shift $shift) {
		return $user->user_type == Admin::class;
	}
	
	public function deleteAll(User $user) {
		
		return $user->user_type == Admin::class;
		
	}
	
	/**
	 * Determine whether the user can restore the shift.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Shift $shift
	 *
	 * @return mixed
	 */
	public function restore(User $user, Shift $shift) {
		//
	}
	
	/**
	 * Determine whether the user can permanently delete the shift.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Shift $shift
	 *
	 * @return mixed
	 */
	public function forceDelete(User $user, Shift $shift) {
		//
	}
}
