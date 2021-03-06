<?php
namespace App\Policies;
use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\Kitchen;
use Illuminate\Auth\Access\HandlesAuthorization;
class KitchenPolicy {
	use HandlesAuthorization;

	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}
	/**
	 * Determine whether the user can view the kitchen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Kitchen $kitchen
	 * @return mixed
	 */
	public function view(User $user, Kitchen $kitchen) {
		//
	}

	/**
	 * Determine whether the user can create kitchens.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
        return $user->user_type == Admin::class;
    }

	/**
	 * Determine whether the user can update the kitchen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Kitchen $kitchen
	 * @return mixed
	 */
	public function update(User $user, Kitchen $kitchen) {
		if ($user->user_type == Kitchen::class && $user->user_id == $kitchen->id) {
			return true;
		}
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can delete the kitchen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Kitchen $kitchen
	 * @return mixed
	 */
	public function delete(User $user, Kitchen $kitchen) {
		if ($user->user_type == Kitchen::class && $user->user_id == $kitchen->id) {
			return true;
		}
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can restore the kitchen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Kitchen $kitchen
	 * @return mixed
	 */
	public function restore(User $user, Kitchen $kitchen) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the kitchen.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Kitchen $kitchen
	 * @return mixed
	 */
	public function forceDelete(User $user, Kitchen $kitchen) {
		//
	}
}
