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
        if ($request->user()->user_type === Developer::class) {
            return $next($request);
        }
        $allowedTypes = str_contains($userType, '')
            ? array_map('trim', explode('|', $userType))
            : [$userType];
        if (! in_array($request->user()->user_type, $allowedTypes, true)) {
            abort(403, 'Access denied');
        }

        return $next($request);
	}
}
