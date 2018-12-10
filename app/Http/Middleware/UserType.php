<?php

namespace App\Http\Middleware;

use App\Models\Developer;
use Closure;

class UserType {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $userType) {
		if ($request->user()->user_type == Developer::class){
			return $next($request);
		}

		 if ($request->user()->user_type != $userType) {
			return abort(403, 'Access denied');
		}
		return $next($request);

	}
}
