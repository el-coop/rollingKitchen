<?php

namespace App\Policies;

use App\Models\Developer;
use App\Models\ProductPhoto;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPhotoPolicy {
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

    public function view(User $user, ProductPhoto $productPhoto) {
        return $user->can('update', $productPhoto->product->application);
    }
}
