<?php

namespace App\Http\Middleware;

use App\Exceptions\FeatureDisabledException;
use Closure;
use Illuminate\Http\Request;

class FeatureEnabledMiddleware
{
    /**
     * Feature check is open to association
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $feature See database column name
     *
     * @return mixed | void
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next, $feature)
    {
        throw_if(!$request->organization->get($feature . '_enabled'), FeatureDisabledException::class);

        return $next($request);
    }
}
