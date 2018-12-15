<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Spatie\Valuestore\Valuestore;

class SettingsServiceProvider extends ServiceProvider {


    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        if (today() > Carbon::create(date('Y'), 12, 14)) {
            $year = date('Y') + 1;
        } else {
            $year = date('Y');
        }
        $this->app->settings->put('registration_year', $year);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('settings', function ($app) {
            return Valuestore::make(database_path('settings.json'));
        });
    }
}
