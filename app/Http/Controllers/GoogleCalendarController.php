<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    protected $googleCalendar;

    public function __construct(GoogleCalendarService $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    /**
     * Redirect to Google OAuth consent screen
     */
    public function redirect()
    {
        try {
            $authUrl = $this->googleCalendar->getAuthUrl();
            return redirect($authUrl);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to initiate Google Calendar authorization: ' . $e->getMessage());
        }
    }

    /**
     * Handle OAuth callback from Google
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('dashboard')->with('error', 'Authorization failed: ' . $request->error);
        }

        if (!$request->has('code')) {
            return redirect()->route('dashboard')->with('error', 'No authorization code received');
        }

        try {
            $this->googleCalendar->authenticate($request->code);
            return redirect()->route('dashboard')->with('success', 'Google Calendar connected successfully! You can now schedule interviews.');
        } catch (\Exception $e) {
            Log::error('Google Calendar OAuth Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Failed to connect Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Google Calendar
     */
    public function disconnect()
    {
        try {
            $this->googleCalendar->disconnect();
            return redirect()->route('dashboard')->with('success', 'Google Calendar disconnected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to disconnect: ' . $e->getMessage());
        }
    }

    /**
     * Check connection status
     */
    public function status()
    {
        $isConnected = $this->googleCalendar->isConnected();
        
        return response()->json([
            'connected' => $isConnected,
            'message' => $isConnected ? 'Google Calendar is connected' : 'Google Calendar is not connected'
        ]);
    }
}
