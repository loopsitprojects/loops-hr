<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::find(2);
if ($user) {
    if ($user->email !== 'admin@loopshr.com') {
        echo "Warning: User 2 is not admin@loopshr.com. It is " . $user->email . ". Aborting update just in case.\n";
    } else {
        $user->role = User::ROLE_SUPER_ADMIN;
        $user->save();
        echo "Updated User 2 (" . $user->email . ") role to " . $user->role . ".\n";
    }
} else {
    echo "User 2 not found.\n";
}
