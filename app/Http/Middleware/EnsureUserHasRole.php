<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = array_map(fn (string $r) => Role::from($r), $roles);

        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
