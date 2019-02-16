<?php

namespace App\Policies;

use App\Models\Developer;
use App\Models\User;
use App\Models\TaxReview;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxReviewPolicy {
	use HandlesAuthorization;
	
	public function before($user, $ability) {
		if ($user->user_type == Developer::class) {
			return true;
		}
	}
	
	/**
	 * Determine whether the user can view the tax review.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\TaxReview $taxReview
	 * @return mixed
	 */
	public function view(User $user, TaxReview $taxReview) {
		return $user->can('view', $taxReview->worker);
	}
	
	/**
	 * Determine whether the user can create tax reviews.
	 *
	 * @param  \App\Models\User $user
	 * @return mixed
	 */
	public function create(User $user) {
		//
	}
	
	/**
	 * Determine whether the user can update the tax review.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\TaxReview $taxReview
	 * @return mixed
	 */
	public function update(User $user, TaxReview $taxReview) {
		//
	}
	
	/**
	 * Determine whether the user can delete the tax review.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\TaxReview $taxReview
	 * @return mixed
	 */
	public function delete(User $user, TaxReview $taxReview) {
		//
	}
	
	/**
	 * Determine whether the user can restore the tax review.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\TaxReview $taxReview
	 * @return mixed
	 */
	public function restore(User $user, TaxReview $taxReview) {
		//
	}
	
	/**
	 * Determine whether the user can permanently delete the tax review.
	 *
	 * @param  \App\Models\User $user
	 * @param  \App\Models\TaxReview $taxReview
	 * @return mixed
	 */
	public function forceDelete(User $user, TaxReview $taxReview) {
		//
	}
}
