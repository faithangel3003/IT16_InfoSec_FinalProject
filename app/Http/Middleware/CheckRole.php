<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed roles for this route (can be comma-separated)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // Admin always has access to everything
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Security role has access to security-related routes
        if ($userRole === 'security' && in_array('security', $roles)) {
            return $next($request);
        }

        // Flatten roles array (handles both 'role:a,b' and 'role:a:b' formats)
        $allowedRoles = [];
        foreach ($roles as $role) {
            // Split by comma in case roles are comma-separated
            $splitRoles = explode(',', $role);
            $allowedRoles = array_merge($allowedRoles, $splitRoles);
        }

        // Check if user's role is in the allowed roles
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. You do not have permission to perform this action.');
    }
}
