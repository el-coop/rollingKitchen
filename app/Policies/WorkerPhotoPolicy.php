<?php

namespace App\Policies;

use App\Models\Developer;
use App\Models\User;
use App\Models\WorkerPhoto;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkerPhotoPolicy {
	use HandlesAuthorization;
	
	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}
	
	/**
	 * Determine whether the user can view the worker photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkerPhoto $workerPhoto
	 * @return mixed
	 */
	public function view(User $user, WorkerPhoto $workerPhoto) {
		return $user->can('update', $workerPhoto->worker);
	}
	
	/**
	 * Determine whether the user can create worker photos.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		//
	}
	
	/**
	 * Determine whether the user can update the worker photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkerPhoto $workerPhoto
	 * @return mixed
	 */
	public function update(User $user, WorkerPhoto $workerPhoto) {
		//
	}
	
	/**
	 * Determine whether the user can delete the worker photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkerPhoto $workerPhoto
	 * @return mixed
	 */
	public function delete(User $user, WorkerPhoto $workerPhoto) {
		//
	}
	
	/**
	 * Determine whether the user can restore the worker photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkerPhoto $workerPhoto
	 * @return mixed
	 */
	public function restore(User $user, WorkerPhoto $workerPhoto) {
		//
	}
	
	/**
	 * Determine whether the user can permanently delete the worker photo.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkerPhoto $workerPhoto
	 * @return mixed
	 */
	public function forceDelete(User $user, WorkerPhoto $workerPhoto) {
		//
	}
}
