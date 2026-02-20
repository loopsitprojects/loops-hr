<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewCandidateApplication;

class CandidateWebhookController extends Controller
{
    public function handleWPFormsWebhook(Request $request)
    {
        $startTime = microtime(true);
        try {
            Log::info('Webhook Received', [
                'headers' => $request->headers->all(),
                'payload' => $request->all()
            ]);

            // Validate API token if set
            $expectedToken = env('WPFORMS_WEBHOOK_TOKEN');
            if ($expectedToken && $request->header('X-Webhook-Token') !== $expectedToken) {
                Log::warning('Invalid webhook token');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Extract form data
            $fields = $request->input('fields', []);
            if (empty($fields)) {
                $fields = $request->all();
            }
            
            // Map fields with expanded search terms
            $name = $this->getFieldValue($fields, 'full_name') ?? $this->getFieldValue($fields, 'name');
            $email = $this->getFieldValue($fields, 'email');
            $phone = $this->getFieldValue($fields, 'phone_number') ?? $this->getFieldValue($fields, 'phone');
            
            // Robust salary detection
            $expectedSalary = $this->getFieldValue($fields, 'expected_salary') 
                           ?? $this->getFieldValue($fields, 'salary')
                           ?? $this->getFieldValue($fields, 'expected_pay');
            
            $postName = $this->getFieldValue($fields, 'post_name') ?? $this->getFieldValue($fields, 'designation');
            $departmentName = $this->getFieldValue($fields, 'department');
            
            $cvUrl = $this->getFieldValue($fields, 'upload_your_cv') 
                  ?? $this->getFieldValue($fields, 'cv')
                  ?? $this->getFieldValue($fields, 'resume');
            
            if (is_array($cvUrl)) {
                $cvUrl = $cvUrl[0] ?? null;
            }

            $message = $this->getFieldValue($fields, 'message') ?? $this->getFieldValue($fields, 'comments');

            // Validate required fields
            if (!$name || !$email) {
                Log::error('Missing required fields', [
                    'name' => $name, 
                    'email' => $email,
                    'available_keys' => array_keys($fields)
                ]);
                return response()->json(['error' => 'Missing required fields: name and email'], 400);
            }

            // Find or create designation and department
            $designation = null;
            $department = null;
            
            // 1. Identify or Create Department
            if ($departmentName) {
                $department = Department::where('name', 'LIKE', trim($departmentName))->first();
                if (!$department) {
                    $department = Department::create(['name' => trim($departmentName)]);
                    Log::info('New department created', ['name' => $departmentName]);
                }
            }

            // 2. Identify or Create Designation in that Department
            if ($postName) {
                $postName = trim($postName);
                
                // If we have a department, look strictly there first
                if ($department) {
                    $designation = Designation::where('name', 'LIKE', $postName)
                        ->where('department_id', $department->id)
                        ->first();
                } else {
                    // Otherwise look anywhere
                    $designation = Designation::where('name', 'LIKE', $postName)->first();
                }

                if (!$designation) {
                    // Create designation in the identified department (or default)
                    if (!$department) {
                        $defaultDepartmentId = env('WPFORMS_DEFAULT_DEPARTMENT_ID', 1);
                        $department = Department::find($defaultDepartmentId) ?? Department::first();
                    }
                    
                    if ($department) {
                        $designation = Designation::create([
                            'name' => $postName,
                            'department_id' => $department->id,
                            'is_active' => true
                        ]);
                        Log::info('New designation created', [
                            'name' => $postName, 
                            'department' => $department->name
                        ]);
                    }
                }
            }

            // Use default fallback if still no designation
            if (!$designation) {
                $designation = Designation::find(env('WPFORMS_DEFAULT_DESIGNATION_ID', 1));
            }
            
            if ($designation && !$department) {
                $department = $designation->department;
            }

            // Download and upload CV
            $cvPath = null;
            if ($cvUrl) {
                $cvStart = microtime(true);
                try {
                    $cvPath = $this->downloadAndUploadCV($cvUrl, $name);
                    Log::info('CV processed', ['duration' => round(microtime(true) - $cvStart, 2) . 's']);
                } catch (\Exception $e) {
                    Log::error('CV processing failed', ['url' => $cvUrl, 'error' => $e->getMessage()]);
                }
            }

            // Create candidate
            $candidate = Candidate::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'expected_salary' => $expectedSalary ? preg_replace('/[^0-9.]/', '', $expectedSalary) : null,
                'designation' => $designation ? $designation->name : ($postName ?? 'Generic'),
                'designation_id' => $designation ? $designation->id : null,
                'department_id' => $department ? $department->id : null,
                'cv_path' => $cvPath,
                'hod_comment' => $message,
                'stage' => 'default',
                'status' => 'pending',
            ]);

            Log::info('Candidate created', ['id' => $candidate->id]);

            // Notify Admins
            $notifStart = microtime(true);
            
            // 1. Get Global recipients (Super Admin, HR Admin, Operations Manager)
            $globalRecipients = User::whereIn('role', [
                User::ROLE_SUPER_ADMIN, 
                User::ROLE_HR_ADMIN, 
                User::ROLE_MANAGER
            ])->get();

            // 2. Get Departmental recipients (Managers, HODs)
            $deptRecipients = collect();
            if ($candidate->department_id) {
                $deptRecipients = User::whereIn('role', [
                    User::ROLE_MANAGERS, 
                    User::ROLE_HOD
                ])
                ->where('department_id', $candidate->department_id)
                ->get();
            }

            // Combine and ensure unique users
            $allRecipients = $globalRecipients->concat($deptRecipients)->unique('id');

            if ($allRecipients->count() > 0) {
                Notification::send($allRecipients, new NewCandidateApplication($candidate));
                Log::info('Notifications sent', [
                    'recipients_count' => $allRecipients->count(),
                    'duration' => round(microtime(true) - $notifStart, 2) . 's'
                ]);
            }

            $totalDuration = round(microtime(true) - $startTime, 2);
            Log::info('Webhook completed', ['total_duration' => $totalDuration . 's']);

            return response()->json([
                'success' => true,
                'candidate_id' => $candidate->id,
                'processing_time' => $totalDuration . 's'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Webhook failed', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get field value from WPForms fields array
     */
    private function getFieldValue($fields, $fieldName)
    {
        // WPForms can send data in different formats
        // Try to find by field name (case-insensitive)
        foreach ($fields as $field) {
            if (isset($field['name']) && strtolower($field['name']) === strtolower($fieldName)) {
                return $field['value'] ?? null;
            }
            if (isset($field['label']) && strtolower(str_replace(' ', '_', $field['label'])) === strtolower($fieldName)) {
                return $field['value'] ?? null;
            }
        }

        // Also check if it's sent as direct key-value
        if (isset($fields[$fieldName])) {
            return $fields[$fieldName];
        }

        return null;
    }

    /**
     * Download CV from URL and upload to FTP server
     */
    private function downloadAndUploadCV($url, $candidateName)
    {
        // Download CV from WPForms URL
        $response = Http::timeout(30)->get($url);
        
        if (!$response->successful()) {
            throw new \Exception("Failed to download CV from URL: {$url}");
        }

        // Get file extension from URL or content type
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$extension) {
            $contentType = $response->header('Content-Type');
            $extension = $contentType === 'application/pdf' ? 'pdf' : 'pdf';
        }

        // Generate filename with timestamp
        $timestamp = now()->format('Ymd_His');
        $sanitizedName = preg_replace('/[^A-Za-z0-9_-]/', '_', $candidateName);
        $filename = $timestamp . '_' . $sanitizedName . '_CV.' . $extension;

        // Upload to FTP server
        $path = 'cvs/' . $filename;
        Storage::disk('ftp_cvs')->put($path, $response->body());

        return $path;
    }
}
