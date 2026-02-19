<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_recruitment_tests_data_endpoint()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/recruitment/tests-data');

        $response->assertStatus(200);
    }

    public function test_assessment_public_access_requires_token()
    {
        // Random token
        $response = $this->get('/assessment/invalid-token');
        
        // Should probably return 404 if token not found
        $response->assertStatus(404);
    }
}
