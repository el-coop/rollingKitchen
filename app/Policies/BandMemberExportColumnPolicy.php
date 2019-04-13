<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\BandMemberExportColumn;
use Illuminate\Auth\Access\HandlesAuthorization;

class BandMemberExportColumnPolicy {
	use HandlesAuthorization;

	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the band member export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMemberExportColumn $bandMemberExportColumn
	 * @return mixed
	 */
	public function view(User $user, BandMemberExportColumn $bandMemberExportColumn) {
		//
	}

	/**
	 * Determine whether the user can create band member export columns.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can update the band member export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMemberExportColumn $bandMemberExportColumn
	 * @return mixed
	 */
	public function update(User $user, BandMemberExportColumn $bandMemberExportColumn) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can delete the band member export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMemberExportColumn $bandMemberExportColumn
	 * @return mixed
	 */
	public function delete(User $user, BandMemberExportColumn $bandMemberExportColumn) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can restore the band member export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMemberExportColumn $bandMemberExportColumn
	 * @return mixed
	 */
	public function restore(User $user, BandMemberExportColumn $bandMemberExportColumn) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the band member export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\BandMemberExportColumn $bandMemberExportColumn
	 * @return mixed
	 */
	public function forceDelete(User $user, BandMemberExportColumn $bandMemberExportColumn) {
		//
	}

	public function order(User $user) {
		return $user->user_type == Admin::class;
	}
}
