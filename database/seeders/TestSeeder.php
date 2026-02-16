<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Test::updateOrCreate(
            ['name' => 'AI Design and Video Specialist'],
            [
                'subject' => 'Assessment Task: AI Design and Video Specialist | Loops Integrated',
                'content' => "Dear Candidate,\n\nThank you for your interest in the AI Design and Video Specialist position at Loops Integrated. As part of our recruitment process, we would like to invite you to complete a practical assessment task.\n\n[DETAILED TASK INSTRUCTIONS WILL BE INSERTED HERE]\n\nPlease ensure that your submission is completed and uploaded via the link below by the designated deadline.\n\nShould you have any questions regarding the task, please feel free to reach out to us."
            ]
        );
    }
}
