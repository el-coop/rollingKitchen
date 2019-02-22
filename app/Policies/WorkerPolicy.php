<?php

namespace App\Policies;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkerPolicy {
	use HandlesAuthorization;
	
	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}
	
	/**
	 * Determine whether the user can view the worker.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Worker $worker
	 * @return mixed
	 */
	public function view(User $user, Worker $worker) {
		if ($user->user_type == Worker::class) {
			return $user->user_id == $worker->id;
		}
		return $user->user_type == Admin::class;
	}
	
	/**
	 * Determine whether the user can create workers.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class || ($user->user_type == Worker::class && $user->user->isSupervisor());
	}
	
	/**
	 * Determine whether the user can update the worker.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Worker $worker
	 * @return mixed
	 */
	public function update(User $user, Worker $worker) {
		if ($user->user_type == Worker::class) {
			return $user->user_id == $worker->id || ($worker->isMySupervisor($user));
		}
		return $user->user_type == Admin::class;
	}
	
	/**
	 * Determine whether the user can delete the worker.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Worker $worker
	 * @return mixed
	 */
	public function delete(User $user, Worker $worker) {
		if ($user->user_type == Worker::class) {
			return $user->user_id == $worker->id || ($worker->isMySupervisor($user));
		}
		return $user->user_type == Admin::class;
	}
	
	/**
	 * Determine whether the user can restore the worker.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Worker $worker
	 * @return mixed
	 */
	public function restore(User $user, Worker $worker) {
		//
	}
	
	/**
	 * Determine whether the user can permanently delete the worker.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Worker $worker
	 * @return mixed
	 */
	public function forceDelete(User $user, Worker $worker) {
		//
	}

	public function disapproveAll (User $user) {

		return $user->user_type == Admin::class;
	}
	
	public function pdf(User $user, Worker $worker) {
		return $user->user_type == Admin::class || $user->user_type == Accountant::class;
	}
	
	public function taxReview(User $user, Worker $worker) {
		return $user->user_type == Admin::class;
	}
}
