<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Facades\Http;

// 1. Ensure a designation exists
$dept = Department::firstOrCreate(['name' => 'API Test Dept']);
$designation = Designation::firstOrCreate(
    ['name' => 'API Test Role'],
    ['department_id' => $dept->id]
);

echo "Testing with Designation ID: " . $designation->id . "\n";

// 2. Send Request
$url = 'http://127.0.0.1:8000/api/candidates';
$token = 'loops_hr_secret_api_token_2026';
$file = base_path('storage/app/test_cv.pdf');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-TOKEN: ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'name' => 'API Candidate',
    'email' => 'api.test@example.com',
    'phone' => '1234567890',
    'designation_id' => $designation->id,
    'cv' => new CURLFile($file)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
