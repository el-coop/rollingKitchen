<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Application;
use App\Models\Field;
use App\Models\Kitchen;
use App\Models\Setting;
use App\Policies\ApplicationPolicy;
use App\Policies\FieldPolicy;
use App\Policies\KitchenPolicy;
use App\Policies\SettingPolicy;
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
		Kitchen::class => KitchenPolicy::class,
        Application::class => ApplicationPolicy::class,
        Setting::class => SettingPolicy::class
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
