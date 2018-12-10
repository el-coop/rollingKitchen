<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Developer;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy {
	use HandlesAuthorization;

	public function before($user,$ability){
		if ($user->user_type == Developer::class){
			return true;
		}
	}
	/**
	 * Determine whether the user can view the invoice.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Invoice $invoice
	 * @return mixed
	 */
	public function view(User $user, Invoice $invoice) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can create invoices.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can update the invoice.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Invoice $invoice
	 * @return mixed
	 */
	public function update(User $user, Invoice $invoice) {
		return $user->user_type == Admin::class;
	}

	/**
	 * Determine whether the user can delete the invoice.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Invoice $invoice
	 * @return mixed
	 */
	public function delete(User $user, Invoice $invoice) {
		//
	}

	/**
	 * Determine whether the user can restore the invoice.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Invoice $invoice
	 * @return mixed
	 */
	public function restore(User $user, Invoice $invoice) {
		//
	}

	/**
	 * Determine whether the user can permanently delete the invoice.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\Invoice $invoice
	 * @return mixed
	 */
	public function forceDelete(User $user, Invoice $invoice) {
		//
	}
}
