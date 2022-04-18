<?php

namespace App\Providers;

use App\Models\User;
use Carbon\Carbon;
use ElCoop\Valuestore\Valuestore;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider {
	
	
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot() {
		if (today() > Carbon::create(date('Y'), 11, 14) && $this->app->settings->get('registration_year') == date('Y')) {
			$year = date('Y') + 1;
			$this->app->settings->put('registration_year', $year);
            $users = User::where('checked_info', true)->get();
            foreach ($users as $user){
                $user->checked_info = false;
                $user->save();
            }
		}
	}
	
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->singleton('settings', function ($app) {
			return new Valuestore(database_path('settings.json'));
		});
	}
}
