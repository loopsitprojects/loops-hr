<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@loopshr.com',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);

        // Seed default rejection template
        \App\Models\RejectionTemplate::create([
            'type' => 'default',
            'content' => "Dear [Candidate Name],\n\nThank you for your interest in the position at Loops Integrated. After careful consideration, we have decided to move forward with other candidates who more closely match our current requirements.\n\nWe appreciate the time you took to apply and wish you the best in your career search.",
        ]);
    }
}
