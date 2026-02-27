<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserMailRoutingTest extends TestCase
{
    /**
     * Test that admin@loopshr.com does not receive emails.
     */
    public function test_admin_email_returns_null_for_mail_routing()
    {
        $user = new User(['email' => 'admin@loopshr.com']);
        
        $this->assertNull($user->routeNotificationForMail());
    }

    /**
     * Test that other emails receive emails normally.
     */
    public function test_other_emails_return_email_for_mail_routing()
    {
        $user = new User(['email' => 'other@example.com']);
        
        $this->assertEquals('other@example.com', $user->routeNotificationForMail());
    }
}
