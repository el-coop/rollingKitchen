<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\Debtor;
use Illuminate\Auth\Access\HandlesAuthorization;

class DebtorPolicy {
	use HandlesAuthorization;
	
	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}
	
	/**
	 * Determine whether the user can view the debtor.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Debtor $debtor
	 * @return mixed
	 */
	public function view(User $user, Debtor $debtor) {
		//
	}
	
	/**
	 * Determine whether the user can create debtors.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;
	}
	
	/**
	 * Determine whether the user can update the debtor.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Debtor $debtor
	 * @return mixed
	 */
	public function update(User $user, Debtor $debtor) {
		return $user->user_type == Admin::class;
	}
	
	/**
	 * Determine whether the user can delete the debtor.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Debtor $debtor
	 * @return mixed
	 */
	public function delete(User $user, Debtor $debtor) {
		return $user->user_type == Admin::class;
	}
	
	/**
	 * Determine whether the user can restore the debtor.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Debtor $debtor
	 * @return mixed
	 */
	public function restore(User $user, Debtor $debtor) {
		//
	}
	
	/**
	 * Determine whether the user can permanently delete the debtor.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Debtor $debtor
	 * @return mixed
	 */
	public function forceDelete(User $user, Debtor $debtor) {
		//
	}
}
