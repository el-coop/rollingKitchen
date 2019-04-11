<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\KitchenExportColumn;
use Illuminate\Auth\Access\HandlesAuthorization;

class KitchenExportColumnPolicy {
	use HandlesAuthorization;

	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}
	/**
	 * Determine whether the user can view the kitchen export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\KitchenExportColumn $kitchenExportColumn
	 * @return mixed
	 */
	public function view(User $user, KitchenExportColumn $kitchenExportColumn) {
		//
	}

	/**
	 * Determine whether the user can create kitchen export columns.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can update the kitchen export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\KitchenExportColumn $kitchenExportColumn
	 * @return mixed
	 */
	public function update(User $user, KitchenExportColumn $kitchenExportColumn) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can delete the kitchen export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\KitchenExportColumn $kitchenExportColumn
	 * @return mixed
	 */
	public function delete(User $user, KitchenExportColumn $kitchenExportColumn) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can restore the kitchen export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\KitchenExportColumn $kitchenExportColumn
	 * @return mixed
	 */
	public function restore(User $user, KitchenExportColumn $kitchenExportColumn) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the kitchen export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\KitchenExportColumn $kitchenExportColumn
	 * @return mixed
	 */
	public function forceDelete(User $user, KitchenExportColumn $kitchenExportColumn) {
		//
	}

	public function order(User $user) {
		return $user->user_type == Admin::class;

	}
}
