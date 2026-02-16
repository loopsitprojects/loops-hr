<?php
// Verify Notification Logic
try {
    // 1. Get Admin User
    $admin = \App\Models\User::where('role', 'Super Admin')->first();
    if (!$admin) {
        echo "No Super Admin found.\n";
        exit(1);
    }
    echo "Found Admin: " . $admin->email . "\n";

    // 2. Create Dummy Candidate (without saving to DB to avoid pollution, but ID might be needed for URL generation in notification)
    // We can just instantiate it.
    $candidate = new \App\Models\Candidate([
        'id' => 99999,
        'name' => 'Verification Candidate',
        'department_id' => 1,
        'designation_id' => 1,
        'designation' => 'Verifier',
        'email' => 'verify@test.com'
    ]);

    // 3. Send Notification
    echo "Sending notification...\n";
    \Illuminate\Support\Facades\Notification::send($admin, new \App\Notifications\NewCandidateApplication($candidate));

    // 4. Check Database
    $notification = $admin->notifications()->latest()->first();
    
    if ($notification && $notification->data['message'] === 'New candidate application: Verification Candidate') {
        echo "SUCCESS: Notification found in database.\n";
        echo "Message: " . $notification->data['message'] . "\n";
        
        // Cleanup
        $notification->delete();
        echo "Cleanup: Notification deleted.\n";
    } else {
        echo "FAILURE: Notification not found or data mismatch.\n";
        if ($notification) {
            print_r($notification->toArray());
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
