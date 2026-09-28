<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role?->name;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // User tanpa role: jangan redirect (bisa loop) — tolak saja.
        if ($userRole === null) {
            abort(403, 'Your account has no role assigned.');
        }

        return redirect()->route('customer.dashboard');
    }
}
