<?php

namespace App\Http\Middleware;

use Closure;

class KitchenRegistrationOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$settings = app('settings');

		if  (! $settings->get('general_registration_status')) {

			return redirect()->action('HomeController@show');
		}
        return $next($request);
    }
}
