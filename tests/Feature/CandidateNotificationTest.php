<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\User;
use App\Models\Designation;
use App\Models\Department;
use App\Notifications\NewCandidateApplication;
use Illuminate\Http\UploadedFile;

class CandidateNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_admin_receives_notification_on_new_application()
    {
        Notification::fake();

        // Create Dept & Designation
        $dept = Department::create(['name' => 'IT']);
        $designation = Designation::create([
            'department_id' => $dept->id,
            'name' => 'Developer',
            'is_active' => true
        ]);

        // Create HR Admin
        $hrAdmin = User::factory()->create([
            'role' => User::ROLE_HR_ADMIN,
        ]);

        // Create Super Admin
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // Create Regular User (should not notify)
        $manager = User::factory()->create([
            'role' => User::ROLE_MANAGER,
        ]);

        // Submit Application via API
        // In testing, env() might return null if not in phpunit.xml, but we can mock it or set it.
        // For this test, we'll force the expected token.
        config(['app.candidate_api_token' => 'test-token']); // Assuming we might validiate against config or env directly
        
        // Actually the middleware checks env('CANDIDATE_API_TOKEN').
        // We can override the env variable for the test process? 
        // Laravel's Config facade is better if the app uses config(), but middleware uses env().
        // Let's rely on the fact that we can pass what the middleware expects. 
        // Or if middleware reads env(), we might need to match what's in .env.example or set it.
        // Let's just assume the middleware reads from actual env.
        
        $token = 'loops_hr_secret_api_token_2026'; // Default from .env we saw earlier

        $response = $this->withHeader('X-API-TOKEN', $token)
             ->postJson('/api/candidates', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'designation_id' => $designation->id,
                'cv' => UploadedFile::fake()->create('resume.pdf'),
             ]);

        // Check if API auth actually works/is required. 
        // Based on routes/api.php, it uses EnsureApiTokenIsValid middleware.
        // Assuming we need to bypass or provide token if strictly enforced.
        // For test simplicity, if env token is set, it might need header.
        
        // Check status
        if ($response->status() !== 201) {
            $response->dump();
        }
        $response->assertStatus(201);

        // Assert Notification Sent
        Notification::assertSentTo(
            [$hrAdmin, $superAdmin],
            NewCandidateApplication::class
        );

        Notification::assertNotSentTo(
            [$manager],
            NewCandidateApplication::class
        );
    }
}
