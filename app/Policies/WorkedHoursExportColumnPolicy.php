<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\WorkedHoursExportColumn;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkedHoursExportColumnPolicy {
	use HandlesAuthorization;

	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the worked hours export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkedHoursExportColumn $workedHoursExportColumn
	 * @return mixed
	 */
	public function view(User $user, WorkedHoursExportColumn $workedHoursExportColumn) {
		//
	}

	/**
	 * Determine whether the user can create worked hours export columns.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can update the worked hours export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkedHoursExportColumn $workedHoursExportColumn
	 * @return mixed
	 */
	public function update(User $user, WorkedHoursExportColumn $workedHoursExportColumn) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can delete the worked hours export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkedHoursExportColumn $workedHoursExportColumn
	 * @return mixed
	 */
	public function delete(User $user, WorkedHoursExportColumn $workedHoursExportColumn) {
		return $user->user_type == Admin::class;

	}

	/**
	 * Determine whether the user can restore the worked hours export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkedHoursExportColumn $workedHoursExportColumn
	 * @return mixed
	 */
	public function restore(User $user, WorkedHoursExportColumn $workedHoursExportColumn) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the worked hours export column.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\WorkedHoursExportColumn $workedHoursExportColumn
	 * @return mixed
	 */
	public function forceDelete(User $user, WorkedHoursExportColumn $workedHoursExportColumn) {
		//
	}

	/**
	 * @param User $user
	 * @return bool
	 */
	public function order(User $user){
		return $user->user_type == Admin::class;
	}
}
