<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;
    protected $credentialsPath;
    protected $tokenPath;

    public function __construct()
    {
        $this->client = new Client();
        
        // Path to OAuth2 credentials JSON file
        $this->credentialsPath = storage_path('app/google-oauth-credentials.json');
        
        // Path to store access/refresh tokens
        $this->tokenPath = storage_path('app/google-calendar-token.json');
        
        if (file_exists($this->credentialsPath)) {
            $this->client->setAuthConfig($this->credentialsPath);
            $this->client->addScope(Calendar::CALENDAR);
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            
            // Load previously authorized token if it exists
            if (file_exists($this->tokenPath)) {
                $accessToken = json_decode(file_get_contents($this->tokenPath), true);
                $this->client->setAccessToken($accessToken);
                
                // Refresh the token if it's expired
                if ($this->client->isAccessTokenExpired()) {
                    if ($this->client->getRefreshToken()) {
                        try {
                            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                            $this->saveToken($this->client->getAccessToken());
                        } catch (\Exception $e) {
                            Log::error('Google Calendar Constructor Token Refresh Failed: ' . $e->getMessage());
                            // Only delete if it's strictly an authentication issue
                            $errorMsg = strtolower($e->getMessage());
                            if (str_contains($errorMsg, 'invalid_grant') || str_contains($errorMsg, 'unauthorized_client') || str_contains($errorMsg, 'access_denied')) {
                                if (file_exists($this->tokenPath)) {
                                    unlink($this->tokenPath);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function getAuthUrl()
    {
        if (!file_exists($this->credentialsPath)) {
            throw new \Exception('OAuth credentials file not found. Please add google-oauth-credentials.json to storage/app/');
        }
        
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($accessToken['error'])) {
            throw new \Exception('Error fetching access token: ' . $accessToken['error']);
        }
        
        $this->saveToken($accessToken);
        return $accessToken;
    }

    protected function saveToken($token)
    {
        if (!file_exists(dirname($this->tokenPath))) {
            mkdir(dirname($this->tokenPath), 0700, true);
        }
        file_put_contents($this->tokenPath, json_encode($token));
    }

    protected function getClient()
    {
        if (!file_exists($this->credentialsPath)) {
            return null;
        }

        if (!file_exists($this->tokenPath)) {
            return null;
        }

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                try {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $this->saveToken($this->client->getAccessToken());
                } catch (\Exception $e) {
                    // Token is invalid/revoked
                    Log::error('Google Calendar Token Refresh Failed: ' . $e->getMessage());
                    if (file_exists($this->tokenPath)) {
                        unlink($this->tokenPath);
                    }
                    return null;
                }
            } else {
                return null;
            }
        }

        return $this->client;
    }

    public function createMeetEvent($details)
    {
        $client = $this->getClient();
        if (!$client) {
            throw new \Exception('Google Calendar not connected. Please authorize the application first.');
        }

        $service = new Calendar($client);

        $event = new Event([
            'summary' => $details['summary'],
            'description' => $details['description'],
            'start' => [
                'dateTime' => $details['start_time'], // ISO 8601
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => $details['end_time'],
                'timeZone' => config('app.timezone'),
            ],
            'attendees' => $details['attendees'],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid('meet_', true),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ]
            ],
            'organizer' => [
                'displayName' => 'Loops Integrated',
            ],
        ]);

        $calendarId = 'primary';
        $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1, 'sendUpdates' => 'all']);

        return [
            'htmlLink' => $event->htmlLink,
            'hangoutLink' => $event->hangoutLink,
            'eventId' => $event->id
        ];
    }
    
    public function isConnected()
    {
        return $this->getClient() !== null;
    }

    public function getHODEvents($hodEmail, $startTime, $endTime)
    {
        $client = $this->getClient();
        if (!$client) {
            throw new \Exception('Google Calendar not connected.');
        }

        $service = new Calendar($client);
        
        try {
            // "primary" is the authenticated user (HR). 
            // We want to access the HOD's calendar, so we use their email as the calendarId.
            // This works if the HOD has shared their calendar with the authenticated HR account.
            $calendarId = $hodEmail; 

            $optParams = [
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => $startTime,
                'timeMax' => $endTime,
            ];

            $results = $service->events->listEvents($calendarId, $optParams);
            $events = [];

            foreach ($results->getItems() as $event) {
                // Determine start and end times (could be dateTime or date for all-day)
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date; // All-day event
                }
                
                $end = $event->end->dateTime;
                if (empty($end)) {
                    $end = $event->end->date;
                }

                $events[] = [
                    'start' => $start,
                    'end' => $end,
                    'status' => 'Occupied' // Masking details for privacy
                ];
            }

            return ['success' => true, 'events' => $events];

        } catch (\Google\Service\Exception $e) {
            // Check for 404 (Not Found) -> Likely not shared
            if ($e->getCode() == 404) {
                 return [
                     'success' => false, 
                     'error' => 'Calendar not found or not shared. Please ask the HOD to share their calendar with the HR email.'
                 ];
            }

            // Check for Invalid Grant (Revoked/Expired Token)
            $errors = json_decode($e->getMessage(), true);
            if ($e->getCode() == 401 || (isset($errors['error']) && $errors['error'] === 'invalid_grant')) {
                Log::error('Google Calendar Token Revoked/Expired during API call.');
                if (file_exists($this->tokenPath)) {
                    unlink($this->tokenPath);
                }
                return [
                    'success' => false,
                    'error' => 'Connection token expired. Please reconnect Google Calendar from the Dashboard.'
                ];
            }

            throw $e;
        }
    }
    public function disconnect()
    {
        if (file_exists($this->tokenPath)) {
            unlink($this->tokenPath);
        }
    }
}
