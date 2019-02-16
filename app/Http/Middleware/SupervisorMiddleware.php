<?php

namespace App\Http\Middleware;

use App\Models\Worker;
use Closure;

class SupervisorMiddleware {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if ($request->user()->user_type != Worker::class || !$request->user()->user->isSupervisor()) {
			return abort(403, 'Access denied');
			
		}
		return $next($request);
	}
}
