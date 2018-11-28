<?php

namespace App\Providers;

use App\Http\View\Composers\DashboardComposer;
use App\Http\View\Composers\NumberFormatComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
		View::composer(
			'layouts.dashboard', DashboardComposer::class
		);
		View::composer(
			'*', NumberFormatComposer::class
		);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
