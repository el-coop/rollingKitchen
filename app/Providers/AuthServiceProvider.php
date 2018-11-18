<?php

namespace App\Providers;

use App\Models\Field;
use App\Models\Kitchen;
use App\Policies\FieldPolicy;
use App\Policies\KitchenPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Field::class => FieldPolicy::class,
		Kitchen::class => KitchenPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();

    }
}
