<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::all();
$output = "";
foreach ($users as $user) {
    $output .= "ID: " . $user->id . " | Name: " . $user->name . " | Email: " . $user->email . " | Role: " . $user->role . "\n";
}
file_put_contents('users_roles.txt', $output);
echo "Done.";
