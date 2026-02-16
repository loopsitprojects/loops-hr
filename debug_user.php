<?php
$u = \App\Models\User::where('name', 'Super Admin')->first();
if ($u) {
    echo "ID: " . $u->id . "\n";
    echo "Name: " . $u->name . "\n";
    echo "Email: " . $u->email . "\n";
    echo "Role: " . $u->role . "\n";
    echo "Unread Count: " . $u->unreadNotifications->count() . "\n";
    
    // Check if any notifications exist at all
    $total = $u->notifications()->count();
    echo "Total Notifications: " . $total . "\n";
    
    if ($total > 0) {
        print_r($u->notifications()->latest()->first()->toArray());
    }
} else {
    echo "User 'Super Admin' not found.\n";
    // List all users
    foreach(\App\Models\User::all() as $user) {
        echo "User: {$user->name} (Role: {$user->role})\n";
    }
}
