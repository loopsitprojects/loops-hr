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
    ['name' => 'API Salary Role'],
    ['department_id' => $dept->id]
);

echo "Testing with Designation ID: " . $designation->id . "\n";

// 2. Create dummy PDF
$pdfPath = storage_path('app/test_cv_salary.pdf');
file_put_contents($pdfPath, "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << >> /Contents 4 0 R >>\nendobj\n4 0 obj\n<< /Length 21 >>\nstream\nBT /F1 24 Tf (test) Tj ET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000010 00000 n\n0000000060 00000 n\n0000000115 00000 n\n0000000215 00000 n\ntrailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n286\n%%EOF");


// 3. Send Request
$url = 'http://127.0.0.1:8000/api/candidates';
$token = 'loops_hr_secret_api_token_2026';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-TOKEN: ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'name' => 'Salary Candidate',
    'email' => 'salary.test@example.com',
    'phone' => '1234567890',
    'expected_salary' => '50000',
    'designation_id' => $designation->id,
    'cv' => new CURLFile($pdfPath)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

// Cleanup
@unlink($pdfPath);
