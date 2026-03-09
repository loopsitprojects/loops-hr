<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $departments = Department::with('designations')->get();

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please seed departments first.');
            return;
        }

        // Clear existing candidates to prevent unique email collisions
        Candidate::truncate();

        // Note: DB Enum uses 'joined', UI displays 'Hired'
        $validStages = ['default', 'shortlisted', 'test_sent', 'test_received', '1st_interview', '2nd_interview', 'offer_sent', 'offer_accepted', 'joined', 'rejected'];
        $count = 0;

        foreach ($departments as $department) {
            foreach ($department->designations as $designation) {
                // Create exactly 3 candidates per designation
                for ($i = 0; $i < 3; $i++) {
                    Candidate::create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'phone' => $faker->phoneNumber,
                        'department_id' => $department->id,
                        'designation_id' => $designation->id,
                        'status' => 'Active', // Legacy status field
                        'stage' => $faker->randomElement($validStages),
                        'hod_comment' => $faker->optional(0.7)->sentence(6), // 70% chance of having a comment
                        'parsed_content' => $faker->paragraph(3),
                        'cv_path' => 'cvs/dummy_cv.pdf', // Placeholder
                    ]);
                    $count++;
                }
            }
        }

        $this->command->info("Successfully created {$count} dummy candidates across all designations!");
    }
}
