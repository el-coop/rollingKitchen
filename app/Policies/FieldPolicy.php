<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Field;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FieldPolicy {
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the field.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Field $field
     * @return mixed
     */
    public function view(User $user) {
        return $user->user_type == Admin::class;
    }

    /**
     * Determine whether the user can create fields.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user) {
        return $user->user_type == Admin::class;

    }

    /**
     * Determine whether the user can update the field.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Field $field
     * @return mixed
     */
    public function update(User $user, Field $field) {
        return $user->user_type == Admin::class;

    }

    /**
     * Determine whether the user can delete the field.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Field $field
     * @return mixed
     */
    public function delete(User $user, Field $field) {
        return $user->user_type == Admin::class;

    }

    /**
     * Determine whether the user can restore the field.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Field $field
     * @return mixed
     */
    public function restore(User $user, Field $field) {
        //
    }

    /**
     * Determine whether the user can permanently delete the field.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Field $field
     * @return mixed
     */
    public function forceDelete(User $user, Field $field) {
        return $user->user_type == Admin::class;

    }

    /**
     * Determine whether the user can order fields.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function order(User $user){
        return $user->user_type == Admin::class;
    }
}
