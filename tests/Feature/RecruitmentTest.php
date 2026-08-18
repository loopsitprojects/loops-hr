<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecruitmentTest extends TestCase
{
    use RefreshDatabase; 

    public function test_recruitment_dashboard_is_accessible()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/recruitment');

        $response->assertStatus(200);
    }

    public function test_candidate_creation_requires_validation()
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($user)->post('/recruitment', []);

        $response->assertSessionHasErrors(['department_id', 'designation_id', 'cv']);
    }

    public function test_dashboard_department_links_for_authorized_and_unauthorized_users()
    {
        // Create two departments
        $dept1 = Department::create(['name' => 'IT Dept']);
        $dept2 = Department::create(['name' => 'Sales Dept']);

        // Create active designations for each so they show up on the dashboard
        $desig1 = Designation::create(['name' => 'Developer', 'department_id' => $dept1->id, 'is_active' => true]);
        $desig2 = Designation::create(['name' => 'Sales Rep', 'department_id' => $dept2->id, 'is_active' => true]);

        // Scenario 1: HOD of IT Dept
        $hodUser = User::factory()->create([
            'role' => User::ROLE_HOD,
            'department_id' => $dept1->id,
        ]);

        $response = $this->actingAs($hodUser)->get('/dashboard');
        $response->assertStatus(200);
        // HOD has privilege for IT Dept, so it should be a link
        $response->assertSee(route('recruitment.department', $dept1));
        // HOD does not have privilege for Sales Dept, so it should not be a link
        $response->assertDontSee(route('recruitment.department', $dept2));
        // HOD should see designation links for IT Dept
        $response->assertSee(route('recruitment.designation', [$dept1, $desig1]));
        // HOD should not see designation links for Sales Dept
        $response->assertDontSee(route('recruitment.designation', [$dept2, $desig2]));

        // Scenario 2: Super Admin
        $adminUser = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($adminUser)->get('/dashboard');
        $response->assertStatus(200);
        // Admin has privilege for both
        $response->assertSee(route('recruitment.department', $dept1));
        $response->assertSee(route('recruitment.department', $dept2));
        // Admin should see both designation links
        $response->assertSee(route('recruitment.designation', [$dept1, $desig1]));
        $response->assertSee(route('recruitment.designation', [$dept2, $desig2]));
    }

    public function test_candidate_feedback_crud_and_permissions()
    {
        $dept = Department::create(['name' => 'IT']);
        $designation = Designation::create(['name' => 'Developer', 'department_id' => $dept->id]);
        $candidate = \App\Models\Candidate::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'designation_id' => $designation->id,
            'department_id' => $dept->id,
            'designation' => 'Developer',
            'status' => 'pending',
            'stage' => 'default',
            'cv_path' => 'cv.pdf',
        ]);

        $user1 = User::factory()->create(['role' => User::ROLE_HOD]);
        $user2 = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // 1. Any user can give feedback
        $response1 = $this->actingAs($user1)->post(route('recruitment.storeFeedback', $candidate), [
            'feedback' => 'First feedback comment'
        ]);
        $response1->assertStatus(200);
        $this->assertDatabaseHas('candidate_feedbacks', [
            'candidate_id' => $candidate->id,
            'user_id' => $user1->id,
            'feedback' => 'First feedback comment'
        ]);

        $feedbackId = $response1->json('feedback.id');

        // 2. User2 cannot edit or delete User1's feedback
        $response2 = $this->actingAs($user2)->patch(route('recruitment.updateFeedback', $feedbackId), [
            'feedback' => 'Attempted update'
        ]);
        $response2->assertStatus(403);

        $response3 = $this->actingAs($user2)->delete(route('recruitment.destroyFeedback', $feedbackId));
        $response3->assertStatus(403);

        // 3. User1 can edit and delete their own feedback
        $response4 = $this->actingAs($user1)->patch(route('recruitment.updateFeedback', $feedbackId), [
            'feedback' => 'Updated feedback comment'
        ]);
        $response4->assertStatus(200);
        $this->assertDatabaseHas('candidate_feedbacks', [
            'id' => $feedbackId,
            'feedback' => 'Updated feedback comment'
        ]);

        // 4. Super Admin can edit and delete User1's feedback
        $response5 = $this->actingAs($superAdmin)->patch(route('recruitment.updateFeedback', $feedbackId), [
            'feedback' => 'Admin updated comment'
        ]);
        $response5->assertStatus(200);
        $this->assertDatabaseHas('candidate_feedbacks', [
            'id' => $feedbackId,
            'feedback' => 'Admin updated comment'
        ]);

        $response6 = $this->actingAs($superAdmin)->delete(route('recruitment.destroyFeedback', $feedbackId));
        $response6->assertStatus(200);
        $this->assertDatabaseMissing('candidate_feedbacks', [
            'id' => $feedbackId
        ]);
    }

    public function test_candidate_stage_change_requires_rating_for_advanced_stages()
    {
        $dept = Department::create(['name' => 'Engineering']);
        $designation = Designation::create(['name' => 'Software Engineer', 'department_id' => $dept->id]);
        $candidate = \App\Models\Candidate::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'designation_id' => $designation->id,
            'department_id' => $dept->id,
            'designation' => 'Software Engineer',
            'status' => 'pending',
            'stage' => 'default',
            'cv_path' => 'cv.pdf',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $hod = User::factory()->create(['role' => User::ROLE_HOD, 'department_id' => $dept->id]);

        // 1. Transition to 1st_interview should succeed without rating
        $res1 = $this->actingAs($admin)->patch(route('recruitment.updateCandidate', $candidate), [
            'field' => 'stage',
            'value' => '1st_interview',
        ]);
        $res1->assertStatus(200);
        $this->assertEquals('1st_interview', $candidate->fresh()->stage);

        // 2. Super Admin & HR Admin CAN set stage to 'rejected' without rating
        $resRejectAdmin = $this->actingAs($admin)->patch(route('recruitment.updateCandidate', $candidate), [
            'field' => 'stage',
            'value' => 'rejected',
        ]);
        $resRejectAdmin->assertStatus(200);
        $this->assertEquals('rejected', $candidate->fresh()->stage);

        // Reset stage back to 1st_interview
        $candidate->update(['stage' => '1st_interview']);

        // 3. Other mandatory stages (2nd_interview, offer_sent, offer_accepted, joined) fail without rating for Admin
        $mandatoryStagesNoReject = ['2nd_interview', 'offer_sent', 'offer_accepted', 'joined'];
        foreach ($mandatoryStagesNoReject as $stage) {
            $res = $this->actingAs($admin)->patch(route('recruitment.updateCandidate', $candidate), [
                'field' => 'stage',
                'value' => $stage,
            ]);
            $res->assertStatus(422);
            $res->assertJsonStructure(['error']);
        }

        // 4. HOD cannot set 2nd_interview or rejected without a rating
        $resHodReject = $this->actingAs($hod)->patch(route('recruitment.updateCandidate', $candidate), [
            'field' => 'stage',
            'value' => 'rejected',
        ]);
        $resHodReject->assertStatus(422);

        // 5. Rate candidate
        $candidate->update(['rating' => 4.0]);

        // 6. Now transition to all mandatory rating stages should succeed
        $allMandatoryStages = ['2nd_interview', 'offer_sent', 'offer_accepted', 'joined', 'rejected'];
        foreach ($allMandatoryStages as $stage) {
            $res = $this->actingAs($admin)->patch(route('recruitment.updateCandidate', $candidate), [
                'field' => 'stage',
                'value' => $stage,
            ]);
            $res->assertStatus(200);
            $this->assertEquals($stage, $candidate->fresh()->stage);
        }
    }
}

