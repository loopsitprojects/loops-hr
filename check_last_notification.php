<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$notifications = DB::table('notifications')->orderBy('created_at', 'desc')->limit(1)->get();

if ($notifications->count() > 0) {
    foreach ($notifications as $notification) {
        echo "ID: " . $notification->id . "\n";
        echo "Type: " . $notification->type . "\n";
        echo "Created: " . $notification->created_at . "\n";
        echo "Notifiable Type: " . $notification->notifiable_type . "\n";
        echo "Notifiable ID: " . $notification->notifiable_id . "\n";
        // Check if read_at is null
        echo "Read At: " . ($notification->read_at ?? 'NULL') . "\n";
    }
} else {
    echo "No notifications found.";
}
