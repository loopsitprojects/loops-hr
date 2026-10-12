<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateAssessment;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    private function createCandidateAssessment(array $attributes = []): CandidateAssessment
    {
        $dept = Department::create(['name' => 'Engineering']);
        $designation = Designation::create(['name' => 'Developer', 'department_id' => $dept->id]);
        $candidate = Candidate::create([
            'name' => 'Jane Candidate',
            'email' => 'jane@example.com',
            'designation_id' => $designation->id,
            'department_id' => $dept->id,
            'designation' => 'Developer',
            'status' => 'Assessment Sent',
            'stage' => 'test_sent',
            'cv_path' => 'cv.pdf',
        ]);
        $test = Test::create([
            'name' => 'Fullstack Challenge',
            'subject' => 'Web Development',
            'content' => 'Complete the tasks and submit your Google Drive link.',
        ]);

        return CandidateAssessment::create(array_merge([
            'candidate_id' => $candidate->id,
            'test_id' => $test->id,
            'status' => 'Sent',
            'token' => 'sample-valid-token-12345',
            'sent_at' => now(),
        ], $attributes));
    }

    public function test_recruitment_tests_data_endpoint()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/recruitment/tests-data');

        $response->assertStatus(200);
    }

    public function test_assessment_public_access_requires_token()
    {
        $response = $this->get('/assessment/invalid-token');
        $response->assertStatus(404);
    }

    public function test_assessment_page_renders_with_google_drive_requirements_and_confirmation_modal()
    {
        $assessment = $this->createCandidateAssessment();

        $response = $this->get('/assessment/' . $assessment->token);

        $response->assertStatus(200);
        $response->assertSee('Google Drive Submission Link');
        $response->assertSee('Google Drive Only');
        $response->assertSee('Are you sure the Google Drive link is shareable and has view access for evaluation?');
        $response->assertSee('Confirm Submission');
    }

    public function test_assessment_submission_rejects_non_google_drive_links()
    {
        $assessment = $this->createCandidateAssessment();

        $invalidUrls = [
            'https://dropbox.com/s/12345/file.zip',
            'https://canva.com/design/DAF12345/view',
            'https://github.com/user/repo',
            'https://not-google.com/test',
            'random-text-string',
        ];

        foreach ($invalidUrls as $url) {
            $response = $this->post('/assessment/' . $assessment->token . '/submit', [
                'submission_links' => $url,
            ]);

            $response->assertSessionHasErrors('submission_links');
        }

        $assessment->refresh();
        $this->assertEquals('Sent', $assessment->status);
    }

    public function test_assessment_submission_rejects_root_google_drive_without_file_or_folder()
    {
        $assessment = $this->createCandidateAssessment();

        $response = $this->post('/assessment/' . $assessment->token . '/submit', [
            'submission_links' => 'https://drive.google.com/',
        ]);

        $response->assertSessionHasErrors('submission_links');
    }

    public function test_assessment_submission_accepts_valid_google_drive_link()
    {
        Notification::fake();
        $assessment = $this->createCandidateAssessment();

        $validUrl = 'https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/view?usp=sharing';

        $response = $this->post('/assessment/' . $assessment->token . '/submit', [
            'submission_links' => $validUrl,
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $assessment->refresh();
        $this->assertEquals('Submitted', $assessment->status);
        $this->assertEquals($validUrl, $assessment->submission_link);
        $this->assertEquals('test_received', $assessment->candidate->stage);
        $this->assertEquals('Assessment Submitted', $assessment->candidate->status);
    }

    public function test_assessment_submission_accepts_and_normalizes_schemeless_google_drive_link()
    {
        Notification::fake();
        $assessment = $this->createCandidateAssessment();

        $rawUrl = 'drive.google.com/drive/folders/1a2b3c4d5e6f7g';

        $response = $this->post('/assessment/' . $assessment->token . '/submit', [
            'submission_links' => $rawUrl,
        ]);

        $response->assertSessionHas('success');
        $assessment->refresh();
        $this->assertEquals('https://' . $rawUrl, $assessment->submission_link);
    }

    public function test_assessment_submission_accepts_google_docs_link()
    {
        Notification::fake();
        $assessment = $this->createCandidateAssessment();

        $docsUrl = 'https://docs.google.com/document/d/1jK_AbCdEfGhIjKlMnOp/edit?usp=sharing';

        $response = $this->post('/assessment/' . $assessment->token . '/submit', [
            'submission_links' => $docsUrl,
        ]);

        $response->assertSessionHas('success');
        $assessment->refresh();
        $this->assertEquals($docsUrl, $assessment->submission_link);
    }
}
