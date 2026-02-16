<?php
// Find user by NAME not role
$admin = \App\Models\User::where('name', 'Super Admin')->first();

if (!$admin) {
    echo "ERROR: User with name 'Super Admin' not found.\n";
    echo "Available users:\n";
    foreach(\App\Models\User::all() as $u) {
        echo "  - Name: {$u->name}, Role: {$u->role}, ID: {$u->id}\n";
    }
    exit(1);
}

echo "Found user: {$admin->name} (ID: {$admin->id}, Role: {$admin->role})\n";

// Create notification
$admin->notifications()->create([
    'id' => \Illuminate\Support\Str::uuid()->toString(),
    'type' => \App\Notifications\NewCandidateApplication::class,
    'data' => [
        'candidate_id' => 99999,
        'name' => 'John Doe Test Candidate',
        'designation' => 'Software Engineer',
        'message' => 'New candidate application: John Doe Test Candidate',
        'url' => route('recruitment.index'),
    ],
    'read_at' => null,
]);

echo "SUCCESS: Notification created for {$admin->name}\n";
echo "Unread count: " . $admin->unreadNotifications()->count() . "\n";
