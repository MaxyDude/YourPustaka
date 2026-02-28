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
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            // Return JSON for AJAX requests, redirect otherwise
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect('login');
        }

        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Return JSON for AJAX requests, redirect otherwise
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke resource ini'], 403);
        }
        return redirect('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
    }
}
