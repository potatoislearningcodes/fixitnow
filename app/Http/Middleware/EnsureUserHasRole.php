<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage in routes/web.php:
 *   Route::middleware(['auth', 'role:admin'])->group(function () { ... });
 *
 * Register the alias in bootstrap/app.php (Laravel 11) or app/Http/Kernel.php
 * (Laravel 10) — see the README in this template for exact lines to add.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have access to this page.');
        }

        return $next($request);
    }
}
