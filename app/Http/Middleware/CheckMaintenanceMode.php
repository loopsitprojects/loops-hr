<?php

namespace App\Http\Middleware;

use App\Services\MaintenanceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!MaintenanceService::isEnabled()) {
            return $next($request);
        }

        // Exempt routes
        if ($request->is('maintenance') ||
            $request->is('login') ||
            $request->is('logout') ||
            $request->is('system/maintenance/toggle') ||
            $request->is('assessment/*') ||
            $request->is('build/*') ||
            $request->is('loops-icon.png')) {
            return $next($request);
        }

        // Super Admin users bypass maintenance mode
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return $next($request);
        }

        // Log out non-super admin users if logged in
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'The system is currently in maintenance mode. Please try again later.'
            ], 503);
        }

        return redirect()->route('maintenance');
    }
}
