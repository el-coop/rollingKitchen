<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\BandPaymentExportColumn;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandPaymentExportColumnPolicy {
	use HandlesAuthorization;

	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the band payment export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandPaymentExportColumn $bandPaymentExportColumn
	 * @return mixed
	 */
	public function view(User $user, BandPaymentExportColumn $bandPaymentExportColumn) {
		//
	}

	/**
	 * Determine whether the user can create band payment export columns.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can update the band payment export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandPaymentExportColumn $bandPaymentExportColumn
	 * @return mixed
	 */
	public function update(User $user, BandPaymentExportColumn $bandPaymentExportColumn) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can delete the band payment export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandPaymentExportColumn $bandPaymentExportColumn
	 * @return mixed
	 */
	public function delete(User $user, BandPaymentExportColumn $bandPaymentExportColumn) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can restore the band payment export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandPaymentExportColumn $bandPaymentExportColumn
	 * @return mixed
	 */
	public function restore(User $user, BandPaymentExportColumn $bandPaymentExportColumn) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the band payment export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandPaymentExportColumn $bandPaymentExportColumn
	 * @return mixed
	 */
	public function forceDelete(User $user, BandPaymentExportColumn $bandPaymentExportColumn) {
		//
	}

	public function order(User $user){
		return $user->user_type == Admin::class;
	}
}
