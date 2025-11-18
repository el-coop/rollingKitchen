<?php

namespace App\Policies;

use App\Models\ApplicationSketch;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationSketchPolicy {
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {
        //
    }

    public function before($user, $ability) {
        if ($user->user_type == Developer::class) {
            return true;
        }
    }

    public function view(User $user, ApplicationSketch $applicationSketch) {
        return $user->can('update', $applicationSketch->application);
    }
}
