<?php
// Create Persistent Test Notification
try {
    // 1. Get Admin User
    $admin = \App\Models\User::where('role', 'Super Admin')->first();
    if (!$admin) {
        $admin = \App\Models\User::first(); // Fallback
        echo "Warning: Super Admin not found, using first user: " . $admin->email . "\n";
    } else {
        echo "Found Admin: " . $admin->email . "\n";
    }

    // 2. Mock Candidate
    $candidate = new \App\Models\Candidate([
        'id' => 99999,
        'name' => 'Visible Test Candidate',
        'department_id' => 1,
        'designation_id' => 1,
        'designation' => 'Tester',
        'email' => 'visible@test.com'
    ]);

    // 3. Manually create database notification (Bypass Queue)
    echo "Creating notification directly in database...\n";
    
    $admin->notifications()->create([
        'id' => \Illuminate\Support\Str::uuid()->toString(),
        'type' => \App\Notifications\NewCandidateApplication::class,
        'data' => [
            'candidate_id' => $candidate->id,
            'name' => $candidate->name,
            'designation' => $candidate->designation,
            'message' => 'New candidate application: ' . $candidate->name,
            'url' => '#', // Dummy URL
        ],
        'read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "SUCCESS: Test notification created. Refresh your dashboard.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
