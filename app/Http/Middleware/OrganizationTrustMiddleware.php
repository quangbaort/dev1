<?php

namespace App\Http\Middleware;

use App\Exceptions\OrganizationNotVerifyException;
use Closure;
use Illuminate\Http\Request;

class OrganizationTrustMiddleware
{
    use OrganizationMiddlewareTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return void
     * @throws \App\Exceptions\OrganizationNotVerifyException
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next)
    {
        throw_if(!$this->trustedOrgan($request), OrganizationNotVerifyException::class);

        $mergeToRequest = [
            'organization' => collect($this->organization->attributesToArray()),
            'user_role' => $request->user()->isSupperAdmin() ? 0 : $this->organization->pivot->role,
        ];

        return $next($request->merge($mergeToRequest));
    }
}
