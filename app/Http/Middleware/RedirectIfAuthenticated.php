<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @param  string|null $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null) {
		if (Auth::guard($guard)->check()) {
			if (method_exists(Auth::user()->user, 'homePage')){
				return redirect(Auth::user()->user->homePage());
			}
			return redirect()->action('HomeController@show');
		}
		
		return $next($request);
	}
}
