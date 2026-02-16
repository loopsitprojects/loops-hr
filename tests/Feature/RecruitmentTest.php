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
    // use RefreshDatabase; 

    public function test_recruitment_dashboard_is_accessible()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/recruitment');

        $response->assertStatus(200);
    }

    public function test_candidate_creation_requires_validation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/recruitment', []);

        $response->assertSessionHasErrors(['department_id', 'designation_id', 'cv']);
    }
}
