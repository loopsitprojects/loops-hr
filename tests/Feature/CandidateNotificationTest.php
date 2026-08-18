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

        $otherDept = Department::create(['name' => 'Finance']);
        $unnotifiedUser = User::factory()->create([
            'role' => User::ROLE_HOD,
            'department_id' => $otherDept->id,
        ]);

        // Submit Application via API
        config(['app.candidate_api_token' => 'test-token']);

        $token = 'loops_hr_secret_api_token_2026';

        $response = $this->withHeader('X-API-TOKEN', $token)
             ->postJson('/api/candidates', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'designation_id' => $designation->id,
                'cv' => UploadedFile::fake()->create('resume.pdf'),
             ]);

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
            [$unnotifiedUser],
            NewCandidateApplication::class
        );
    }
}
