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
        try {
            Log::info('Raw Webhook Body', ['content' => $request->getContent()]);
            Log::info('Webhook Headers', $request->headers->all());

            // Validate API token if set
            $expectedToken = env('WPFORMS_WEBHOOK_TOKEN');
            if ($expectedToken && $request->header('X-Webhook-Token') !== $expectedToken) {
                Log::warning('Invalid webhook token');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Extract form data from WPForms webhook payload
            $fields = $request->input('fields', []);
            
            // If fields is empty, maybe data is at root level
            if (empty($fields)) {
                Log::info('Fields array is empty, using root request data');
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

            $message = $this->getFieldValue($fields, 'message');

            // Validate required fields
            if (!$name || !$email) {
                Log::error('Missing required fields', [
                    'name' => $name, 
                    'email' => $email,
                    'available_keys' => array_keys($fields)
                ]);
                return response()->json(['error' => 'Missing required fields: name and email'], 400);
            }

            // Find or create designation based on Post Name
            $designation = null;
            $department = null;
            
            if ($departmentName || $postName) {
                // If department name is provided, find or create it
                if ($departmentName) {
                    $department = Department::where('name', 'LIKE', "%{$departmentName}%")->first();
                    if (!$department) {
                        $department = Department::create(['name' => $departmentName]);
                        Log::info('New department created from webhook', ['name' => $departmentName]);
                    }
                }

                if ($postName) {
                    // Try to find designation within the identified department, or any department if none identified
                    $designationQuery = Designation::where('name', 'LIKE', "%{$postName}%");
                    if ($department) {
                        $designationQuery->where('department_id', $department->id);
                    }
                    $designation = $designationQuery->first();
                    
                    if (!$designation) {
                        // Use identified department or default
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
                            Log::info('New designation created from webhook', [
                                'name' => $postName, 
                                'department' => $department->name
                            ]);
                        }
                    }
                }
                
                if ($designation && !$department) {
                    $department = $designation->department;
                }
            }

            // Use default if still not found
            if (!$designation) {
                $designation = Designation::find(env('WPFORMS_DEFAULT_DESIGNATION_ID', 1));
                if ($designation) {
                    $department = $designation->department;
                } else {
                    $department = $department ?? Department::first();
                    // If no designation but we have a department, we could either fail or use a generic one
                    // For now, if we have a department, we'll let it be null or handle as needed
                }
            }

            // Download and upload CV to FTP
            $cvPath = null;
            if ($cvUrl) {
                try {
                    $cvPath = $this->downloadAndUploadCV($cvUrl, $name);
                } catch (\Exception $e) {
                    Log::error('Failed to download/upload CV', ['url' => $cvUrl, 'error' => $e->getMessage()]);
                }
            }

            // Create candidate
            $candidate = Candidate::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'expected_salary' => $expectedSalary ? str_replace(',', '', $expectedSalary) : null,
                'designation' => $designation ? $designation->name : $postName,
                'designation_id' => $designation ? $designation->id : null,
                'department_id' => $department ? $department->id : null,
                'cv_path' => $cvPath,
                'hod_comment' => $message,
                'stage' => 'default',
                'status' => 'pending',
            ]);

            Log::info('Candidate created from WPForms', ['candidate_id' => $candidate->id]);

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

            return response()->json([
                'success' => true,
                'message' => 'Candidate created successfully',
                'candidate_id' => $candidate->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('WPForms webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
        $folder = env('FTP_CV_FOLDER', 'cvs');
        $path = $folder . '/' . $filename;
        Storage::disk('ftp_cvs')->put($path, $response->body());

        return $path;
    }
}
