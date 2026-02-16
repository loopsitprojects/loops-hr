<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class GoogleCalendarTest extends TestCase
{
    public function test_google_calendar_status_endpoint()
    {
        $user = User::factory()->create();

        // This route might check session/DB for token status
        $response = $this->actingAs($user)->get('/google/calendar/status');

        $response->assertStatus(200);
    }
}
