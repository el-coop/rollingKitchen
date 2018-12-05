<?php

namespace App\Http\Middleware;

use Closure;

class UserType {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, ...$userType) {
		if (is_array($userType)){
			if (!in_array($request->user()->user_type, $userType)){
				return abort(403, 'Access denied');
			}
		} elseif ($request->user()->user_type != $userType) {
			return abort(403, 'Access denied');
		}
		return $next($request);

	}
}
