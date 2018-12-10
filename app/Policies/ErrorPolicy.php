<?php

namespace App\Policies;

use App\Models\Developer;
use App\Models\User;
use App\Models\Error;
use Illuminate\Auth\Access\HandlesAuthorization;

class ErrorPolicy {
	use HandlesAuthorization;


	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}

	/**
	 * Determine whether the user can view the error.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Error $error
	 * @return mixed
	 */
	public function view(User $user) {
	}

	/**
	 * Determine whether the user can create errors.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		//
	}

	/**
	 * Determine whether the user can update the error.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Error $error
	 * @return mixed
	 */
	public function update(User $user, Error $error) {
		//
	}

	/**
	 * Determine whether the user can delete the error.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Error $error
	 * @return mixed
	 */
	public function delete(User $user, Error $error) {
	}

	/**
	 * Determine whether the user can restore the error.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Error $error
	 * @return mixed
	 */
	public function restore(User $user, Error $error) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the error.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Error $error
	 * @return mixed
	 */
	public function forceDelete(User $user, Error $error) {
		//
	}


}
