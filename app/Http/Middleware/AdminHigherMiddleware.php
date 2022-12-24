<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class AdminHigherMiddleware
{
    /**
     * Allowed to administrators and above
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed | void
     * @throws AuthorizationException
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next)
    {
        // Administrator can access all
        if ($request->user()->isSupperAdmin()) {
            return $next($request);
        }

        /**
         * Get data of request
         * See: App\Http\Middleware\OrganizationTrustMiddleware
         * */
        throw_if($request->get('user_role') != ADMIN_ROLE, AuthorizationException::class);

        return $next($request);
    }
}
