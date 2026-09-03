<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventPageCache
{
    /**
     * The public pages render an auth-dependent navbar (Login/Register for
     * guests, Browse Listings/Dashboard for signed-in users). Without this,
     * a browser can reuse a page it cached during a logged-in visit and show
     * a logged-out visitor the wrong navbar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request)->setCache([
            'no_store' => true,
            'private' => true,
        ]);
    }
}
