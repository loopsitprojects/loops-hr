<?php

namespace App\Http\Controllers;

use App\Services\MaintenanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Toggle Maintenance Mode (Super Admin only).
     */
    public function toggle(Request $request): RedirectResponse
    {
        if (!Auth::user() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $newState = MaintenanceService::toggle();

        $message = $newState 
            ? 'Maintenance mode ENABLED. Non-super admin users can no longer log in or access the system.' 
            : 'Maintenance mode DISABLED. All users can now access the system.';

        return back()->with('status', $message);
    }
}
