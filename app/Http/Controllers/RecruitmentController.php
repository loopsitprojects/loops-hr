<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Test;
use App\Models\CandidateAssessment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use App\Mail\AssessmentTaskMail;
use App\Mail\CandidateRejectionMail;

use App\Services\GoogleCalendarService;
use App\Models\Interview;
use App\Mail\InterviewScheduled;
use Carbon\Carbon;
use App\Notifications\CandidateStatusChanged;
use App\Notifications\NewCandidateApplication;
use Illuminate\Support\Facades\Notification;

class RecruitmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Department::withCount(['candidates' => function($query) {
            $query->where('is_archived', false)->where('stage', '!=', 'joined');
        }]);

        if ($user->isHOD() || $user->isManagers()) {
             $query->where('id', $user->department_id);
        }

        $departments = $query->orderBy('name', 'asc')->get();
        $rejectionTemplate = \App\Models\RejectionTemplate::where('type', 'default')->first();

        return view('recruitment.index', compact('departments', 'rejectionTemplate'));
    }

    public function department(Department $department)
    {
        $user = auth()->user();
        
        // HOD/Managers can only view their own department
        if (($user->isHOD() || $user->isManagers()) && $user->department_id != $department->id) {
            abort(403, 'Unauthorized access to this department.');
        }

        $department->load(['designations' => function($query) {
            $query->orderBy('name', 'asc')->withCount(['candidates' => function($q) { $q->active(); }]);
        }]);
        
        $designations = $department->designations;
        return view('recruitment.department', compact('department', 'designations'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        Department::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    public function destroyDepartment(Department $department)
    {
        if ($department->designations()->exists()) {
             return redirect()->back()->with('error', 'Cannot delete department with existing roles.');
        }

        if ($department->users()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete department with existing users.');
        }

        if ($department->candidates()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete department with existing candidates.');
        }
        
        $department->delete();
        return redirect()->route('recruitment.index')->with('success', 'Department deleted successfully.');
    }

    public function designation(Department $department, Designation $designation)
    {
        $user = auth()->user();
        
        // HOD/Managers can only view their own department
        if (($user->isHOD() || $user->isManagers()) && $user->department_id != $department->id) {
            abort(403, 'Unauthorized access to this department.');
        }

        $showArchived = request()->has('archived');
        // Original $query = $designation->candidates(); was here.
        
        // For candidates list - Replaced with new logic from instruction
        $currentStage = request('stage', 'all'); // Defined here as it's used in the new query
        $search = request('search'); // Defined here as it's used in the new query

        $query = Candidate::where('designation_id', $designation->id)
            ->where('is_archived', $showArchived);

        if ($currentStage != 'all') {
            $query->where('stage', $currentStage);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // The instruction had a misplaced line here: `$query->where('id', request()->candidate_id);`
        // This line is part of the original logic, but the new query structure implies it should be integrated.
        // Assuming the intent is to keep the candidate_id filter if present.
        if (request()->has('candidate_id')) {
            $query->where('id', request()->candidate_id);
        }

        // Original $candidates = $query->with(['assessments', 'interviews'])->latest()->paginate(10)->withQueryString();
        // Replaced with new logic from instruction
        $candidates = $query->with(['assessments', 'interviews'])->latest()->paginate(10)->withQueryString();
        
        $stages = [
            'default' => 'Default',
            'shortlisted' => 'Shortlisted',
            'test_sent' => 'Test Sent',
            'test_received' => 'Test Received',
            '1st_interview' => '1st Interview',
            '2nd_interview' => '2nd Interview',
            'offer_sent' => 'Offer Sent',
            'offer_accepted' => 'Offer Accepted',
            'joined' => 'Joined',
            'rejected' => 'Rejected',
        ];

        $hods = User::whereIn('role', [
            User::ROLE_HOD, 
            User::ROLE_MANAGER, 
            User::ROLE_MANAGERS
        ])->get();
        
        $rejectionTemplate = \App\Models\RejectionTemplate::where('type', 'default')->first();

        return view('recruitment.designation', compact('department', 'designation', 'candidates', 'showArchived', 'hods', 'stages', 'currentStage', 'rejectionTemplate'));
    }

    public function storeDesignation(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->designations()->create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function destroyDesignation(Designation $designation)
    {
        if ($designation->candidates()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete role with existing candidates.');
        }

        $designation->delete();
        return redirect()->back()->with('success', 'Role removed successfully.');
    }

    public function updateDesignation(Request $request, Designation $designation)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isHR()) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $designation->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Role renamed successfully.');
    }

    public function create(Request $request)
    {
        $departments = Department::with('designations')->get();
        $selectedDepartmentId = $request->query('department_id');
        $selectedDesignationId = $request->query('designation_id');

        return view('recruitment.create', compact('departments', 'selectedDepartmentId', 'selectedDesignationId'));
    }
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Store request data:', $request->all());

        if (!auth()->user()->isAdmin() && !auth()->user()->isHR()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            // Name, Email, Phone are now nullable for auto-extraction
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'cv' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $file = $request->file('cv');
    // Generate filename: timestamp_originalname.pdf
    $originalName = $file->getClientOriginalName();
    $timestamp = now()->format('Ymd_His');
    $filename = $timestamp . '_' . $originalName;
    
    $folder = env('FTP_CV_FOLDER', 'cvs');
    $storagePath = ($folder === '.' || empty($folder)) ? $filename : rtrim($folder, '/') . '/' . $filename;
    
    Log::info('Attempting manual FTP upload', ['path' => $storagePath, 'filename' => $filename]);
    $path = $file->storeAs('', $storagePath, 'ftp_cvs');
    
    if (!$path) {
        Log::error('Manual FTP upload failed', ['path' => $storagePath]);
    } else {
        Log::info('Manual FTP upload successful', ['path' => $path]);
    }
        
        // Auto-Extraction Logic
        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;

        if (empty($name) || empty($email)) {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();
                $filename = $file->getClientOriginalName();
                
                $extractedData = $this->extractCVData($text, $filename);
                
                if (empty($name)) $name = $extractedData['name'];
                if (empty($email)) $email = $extractedData['email'];
                if (empty($phone)) $phone = $extractedData['phone'];
            } catch (\Exception $e) {
                // If parsing fails, fall back to "Unknown Candidate" so we still save the file
                if (empty($name)) $name = 'Unknown Candidate'; 
                Log::error("Auto-extraction failed in store: " . $e->getMessage());
            }
        }
        
        $designationName = Designation::find($request->designation_id)->name ?? 'Unknown';

        // Create Candidate with fallbacks to avoid SQL errors
        $candidate = Candidate::create([
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'name' => $name ?: 'Unknown Candidate', 
            'email' => $email ?: 'noemail_' . time() . '_' . rand(100, 999) . '@extraction.com',
            'phone' => $phone,
            'designation' => $designationName,
            'designation_id' => $request->designation_id,
            'department_id' => $request->department_id,
            'cv_path' => $path,
            'stage' => 'default', 
            'status' => 'pending', 
            'hod_comment' => null,
        ]);

        // Send email notification ONLY to the central HR email address
        Notification::route('mail', 'careers@loopsintegrated.com')
            ->notify(new NewCandidateApplication($candidate));

        // In-app notification: global roles (always) + departmental roles (HOD/Managers of this dept)
        $globalRecipients = User::whereIn('role', [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_HR_ADMIN,
            User::ROLE_MANAGER,
        ])->get();

        $deptRecipients = collect();
        if ($candidate->department_id) {
            $deptRecipients = User::whereIn('role', [User::ROLE_MANAGERS, User::ROLE_HOD])
                ->where('department_id', $candidate->department_id)
                ->get();
        }

        $allRecipients = $globalRecipients->concat($deptRecipients)->unique('id');
        if ($allRecipients->count() > 0) {
            Notification::send($allRecipients, new NewCandidateApplication($candidate));
        }

        return redirect()->route('recruitment.designation', [$request->department_id, $request->designation_id])
            ->with('success', 'Candidate added successfully.');
    }
    public function bulkStore(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isHR()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'cvs' => 'required|array',
            'cvs.*' => 'required|file|mimes:pdf|max:10240',
        ]);

        $processedCount = 0;
        $failedCount = 0;
        $designationName = Designation::find($request->designation_id)->name ?? 'Unknown';

        foreach ($request->file('cvs') as $file) {
            try {
            // Generate filename: timestamp_originalname.pdf
            $originalName = $file->getClientOriginalName();
            $timestamp = now()->format('Ymd_His');
            $filename = $timestamp . '_' . $originalName;
            
            $folder = env('FTP_CV_FOLDER', 'cvs');
            $storagePath = ($folder === '.' || empty($folder)) ? $filename : rtrim($folder, '/') . '/' . $filename;
            
            Log::info('Attempting bulk FTP upload', ['path' => $storagePath, 'filename' => $filename]);
            $path = $file->storeAs('', $storagePath, 'ftp_cvs');
            
            if (!$path) {
                Log::error('Bulk FTP upload failed', ['path' => $storagePath]);
            } else {
                Log::info('Bulk FTP upload successful', ['path' => $path]);
            }
                
                // Extract data
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();
                $filename = $file->getClientOriginalName();
                
                $extractedData = $this->extractCVData($text, $filename);
                
                // Create candidate with fallbacks
                $candidate = Candidate::create([
                    'department_id' => $request->department_id,
                    'designation_id' => $request->designation_id,
                    'name' => $extractedData['name'] ?: 'Unknown Candidate',
                    'email' => $extractedData['email'] ?: 'noemail_' . time() . '_' . rand(100, 999) . '@extraction.com',
                    'phone' => $extractedData['phone'],
                    'designation' => $designationName,
                    'designation_id' => $request->designation_id,
                    'department_id' => $request->department_id,
                    'cv_path' => $path,
                    'stage' => 'default',
                    'status' => 'pending',
                ]);
                
                // Send email notification ONLY to the central HR email address
                Notification::route('mail', 'careers@loopsintegrated.com')
                    ->notify(new NewCandidateApplication($candidate));

                // In-app notification: global roles (always) + departmental roles (HOD/Managers of this dept)
                $globalRecipients = User::whereIn('role', [
                    User::ROLE_SUPER_ADMIN,
                    User::ROLE_HR_ADMIN,
                    User::ROLE_MANAGER,
                ])->get();

                $deptRecipients = collect();
                if ($candidate->department_id) {
                    $deptRecipients = User::whereIn('role', [User::ROLE_MANAGERS, User::ROLE_HOD])
                        ->where('department_id', $candidate->department_id)
                        ->get();
                }

                $allRecipients = $globalRecipients->concat($deptRecipients)->unique('id');
                if ($allRecipients->count() > 0) {
                    Notification::send($allRecipients, new NewCandidateApplication($candidate));
                }
                
                $processedCount++;
            } catch (\Exception $e) {
                Log::error("Bulk upload failed for file {$file->getClientOriginalName()}: " . $e->getMessage());
                $failedCount++;
            }
        }

        return redirect()->route('recruitment.designation', [$request->department_id, $request->designation_id])
            ->with('success', "Bulk upload complete. Processed: $processedCount, Failed: $failedCount");
    }


    public function updateCandidate(Request $request, Candidate $candidate)
    {
        $user = auth()->user();
        
        // HOD, General Managers, and Operations Manager can only edit 'rating', 'hod_comment', and limited 'stage' values
        if ($user->isHOD() || $user->isManagers() || $user->isManager()) {
             // HOD and General Managers must belong to the same department
             if (($user->isHOD() || $user->isManagers()) && $candidate->department_id != $user->department_id) {
                 return response()->json(['error' => 'Unauthorized: Candidate not in your department'], 403);
             }

             // Allow rating, hod_comment, and stage
             if (!in_array($request->field, ['rating', 'hod_comment', 'stage'])) {
                 return response()->json(['error' => 'Unauthorized: You can only edit Rating, Feedback, and Stage'], 403);
             }

             // If editing stage, restrict to specific values
             if ($request->field === 'stage') {
                 $allowedStages = ['shortlisted', '1st_interview', '2nd_interview', 'rejected'];
                 $normalizedValue = strtolower(str_replace(' ', '_', trim($request->value)));
                 
                 if (!in_array($normalizedValue, $allowedStages)) {
                     return response()->json(['error' => 'You can only set stage to: Shortlisted, 1st Interview, 2nd Interview, or Rejected'], 403);
                 }
             }
        }

        // Only Super Admin, HR Admin, HOD, General Managers, and Operations Manager have access
        if (!$user->isAdmin() && !$user->isHR() && !$user->isHOD() && !$user->isManagers() && !$user->isManager()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'field' => 'required|in:designation,stage,hod_comment,name,email,phone,rating,expected_salary',
            'value' => 'nullable|string|max:1000',
        ]);

        $field = $request->field;
        $value = $request->value;

        // Validation for rating (1-5)
        if ($field === 'rating' && !empty($value)) {
            if (!is_numeric($value) || $value < 1 || $value > 5) {
                return response()->json(['error' => 'Rating must be between 1 and 5'], 422);
            }
        }

        // Normalize stage values (convert spaces to underscores and lowercase)
        if ($field === 'stage') {
            $value = strtolower(str_replace(' ', '_', trim($value)));
            
            $validStages = ['default', 'shortlisted', 'test_sent', 'test_received', '1st_interview', '2nd_interview', 'offer_sent', 'offer_accepted', 'joined', 'rejected'];
            if (!in_array($value, $validStages)) {
                return response()->json(['error' => 'Invalid stage value'], 422);
            }

            // Time-to-Hire Logic
            if (in_array($value, ['joined', 'rejected', 'offer_accepted'])) {
                $candidate->finalized_at = now();
            } else {
                $candidate->finalized_at = null;
            }
        }

        $candidate->$field = $value;
        $candidate->save();

        // Notification Logic
        if ($field === 'stage' && ($user->isManager() || $user->isManagers() || $user->isHOD())) {
            $status = $value;
            $changerName = $user->name;
            
            // Get HR Admins and Super Admins
            $admins = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_HR_ADMIN])->get();
            
            if ($admins->count() > 0) {
                Notification::send($admins, new CandidateStatusChanged($candidate, $status, $changerName));
            }
        }

        // Calculate Pipeline Time for dynamic UI update
        $pipelineHtml = '';
        if ($candidate->finalized_at) {
            $diff = $candidate->created_at->diffForHumans($candidate->finalized_at, true);
            if (in_array($candidate->stage, ['joined', 'offer_accepted'])) {
                $pipelineHtml = '<span class="text-[9px] uppercase tracking-wider font-bold text-brand-teal mb-0.5">Hired in</span><span class="text-xs font-bold text-slate-700 dark:text-white">' . $diff . '</span>';
            } elseif ($candidate->stage == 'rejected') {
                $pipelineHtml = '<span class="text-[9px] uppercase tracking-wider font-bold text-red-500 mb-0.5">Rejected after</span><span class="text-xs font-bold text-red-700 dark:text-red-400">' . $diff . '</span>';
            } else {
                $pipelineHtml = '<span class="text-[9px] uppercase tracking-wider font-bold text-slate-400 mb-0.5">Closed in</span><span class="text-xs font-bold text-slate-500 dark:text-slate-400">' . $diff . '</span>';
            }
        } else {
            $diff = $candidate->created_at->diffForHumans(null, true);
            $pipelineHtml = '<span class="text-[9px] uppercase tracking-wider font-bold text-slate-400 mb-0.5">Active</span><span class="text-xs font-bold text-slate-500 dark:text-slate-400">' . $diff . '</span>';
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' updated successfully',
            'value' => $value,
            'pipeline_html' => $pipelineHtml
        ]);
    }

    public function archiveCandidate(Candidate $candidate)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isHR()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $candidate->update(['is_archived' => true]);
        return redirect()->back()->with('success', 'Candidate archived successfully.');
    }

    public function unarchiveCandidate(Candidate $candidate)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isHR()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $candidate->update(['is_archived' => false]);
        return redirect()->back()->with('success', 'Candidate restored from archive.');
    }

    public function bulkArchive(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isHR()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'selected_candidates' => 'required|array',
            'selected_candidates.*' => 'exists:candidates,id',
        ]);

        Candidate::whereIn('id', $request->selected_candidates)->update(['is_archived' => true]);

        return redirect()->back()->with('success', 'Selected candidates archived successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isHR()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'selected_candidates' => 'required|array',
            'selected_candidates.*' => 'exists:candidates,id',
        ]);

        // Get candidates to delete their CV files
        $candidates = Candidate::whereIn('id', $request->selected_candidates)->get();
        
        // Delete CV files from FTP server
        foreach ($candidates as $candidate) {
            if ($candidate->cv_path) {
                try {
                    Storage::disk('ftp_cvs')->delete($candidate->cv_path);
                } catch (\Exception $e) {
                    Log::warning("Failed to delete CV file: {$candidate->cv_path}. Error: " . $e->getMessage());
                }
            }
        }

        // Delete candidate records
        Candidate::whereIn('id', $request->selected_candidates)->delete();

        return redirect()->back()->with('success', 'Selected candidates and their CVs deleted permanently.');
    }

    public function toggleDesignationStatus(Designation $designation)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !$user->isHR()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $designation->is_active = !$designation->is_active;
        $designation->save();

        return response()->json([
            'success' => true,
            'is_active' => $designation->is_active,
            'message' => 'Status updated successfully'
        ]);
    }
    public function previewCV(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('cv');
        
        try {
            // Parse PDF content
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();
            $filename = $file->getClientOriginalName();

            // Extract data
            $extractedData = $this->extractCVData($text, $filename);

            return response()->json([
                'success' => true,
                'data' => $extractedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse CV: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Extract name, email, and phone from CV text
     */
    private function extractCVData($text, $filename = null)
    {
        $name = 'Candidate'; // Fallback
        $email = null;
        $phone = null;

        // Clean text - standardizing newlines
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Try to identify a contact section for better proximity detection
        $contactHeaders = ['contact', 'info', 'personal', 'reach me', 'get in touch', 'details'];
        $bestSection = $text; // Default to whole text
        
        foreach ($contactHeaders as $header) {
            if (preg_match('/(?:\b' . $header . '\b)[\s:]*([\s\S]{1,500})/i', $text, $matches)) {
                $bestSection = $matches[1];
                break;
            }
        }

        // Extract Email - Greedy match for email patterns
        // Added robustness for potential spaces or strange formatting from PDF parsers
        $emailPattern = '/[a-zA-Z0-9._%+-]+(?:\s*@\s*|\s+@\s+)[a-zA-Z0-9.-]+\s*\.\s*[a-zA-Z]{2,}/';
        $standardEmailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        
        if (preg_match($standardEmailPattern, $bestSection, $matches)) {
            $email = $matches[0];
        } elseif (preg_match($standardEmailPattern, $text, $matches)) {
            $email = $matches[0];
        } elseif (preg_match($emailPattern, $bestSection, $matches)) {
            // If we found a spaced email, clean it
            $email = str_replace(' ', '', $matches[0]);
        }

        // Extract Phone
        $phonePatterns = [
            '/\+91[\s-]?\d{10}/',                                      // +91 9876543210
            '/\+\d{1,3}[\s.-]?\(?\d{2,4}\)?[\s.-]?\d{3,4}[\s.-]?\d{4}/', // International
            '/\b\d{3}[\s.-]?\d{4}[\s.-]?\d{3,4}\b/',                   // 077 1876 513 or 0771876513
            '/\b\d{2}[\s.-]?\d{3}[\s.-]?\d{4}\b/',                     // 01 234 5678
            '/\(?0\)?[\s-]?\d{10}/',                                  // (0) 9876543210
            '/\d{10}/',                                               // 9876543210
            '/\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}/',                 // (555) 123-4567
        ];

        // Search in best section first
        foreach ($phonePatterns as $pattern) {
            if (preg_match($pattern, $bestSection, $matches)) {
                $phone = trim($matches[0]);
                break;
            }
        }

        // Fallback to whole text if not found in section
        if (!$phone) {
            foreach ($phonePatterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    $phone = trim($matches[0]);
                    break;
                }
            }
        }

        // Extract Name
        $name = $this->extractName($text, $email, $phone, $filename);

        return [
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ];
    }

    private function extractName($text, $email = null, $phone = null, $filename = null)
    {
        $lines = explode("\n", $text);
        
        // Words that indicate this line is NOT a name (Headers, Labels, etc.)
        $ignoreWords = [
            'resume', 'cv', 'curriculum', 'vitae', 'profile', 'summary', 'objective', 
            'experience', 'work', 'education', 'skills', 'projects', 'languages', 
            'contact', 'reference', 'declaration', 'name', 'email', 'phone', 'address',
            'mobile', 'date', 'place', 'page', 'dob', 'gender', 'nationality',
            'marital', 'status', 'father', 'mother', 'hobbies', 'interests',
            'personal', 'details', 'information', 'history', 'competencies',
            'about', 'me', 'bio', 'availability', 'professional', 'highlights', 'overview'
        ];

        // Words that indicate an Organization/Institution (NOT a person name)
        $organizationKeywords = [
            'university', 'college', 'school', 'institute', 'academy', 'campus',
            'pvt', 'ltd', 'limited', 'private', 'corp', 'corporation', 'inc',
            'company', 'technologies', 'solutions', 'systems', 'services',
            'group', 'foundation', 'trust', 'association', 'society'
        ];

        // Explicit Prefixes to check for first
        $prefixes = ['name:', 'candidate name:', 'full name:', 'applicant:'];

        // Job Titles to ignore
        $designationKeywords = [
            'engineer', 'developer', 'designer', 'manager', 'director', 'executive',
            'analyst', 'consultant', 'specialist', 'coordinator', 'administrator',
            'assistant', 'associate', 'lead', 'senior', 'junior', 'intern', 'trainee',
            'architect', 'programmer', 'technician', 'officer', 'supervisor',
            'head', 'chief', 'president', 'ceo', 'cto', 'cfo', 'coo', 'hr',
            'sales', 'marketing', 'operations', 'finance', 'writer',
            'agent', 'real', 'estate', 'broker', 'representative', 'expert',
            'secretary', 'clerk', 'admin'
        ];

        $candidates = [];

        $count = count($lines);
        for ($index = 0; $index < $count; $index++) {
            $line = trim($lines[$index]);
            if (empty($line)) continue;

             // 1. Explicit Label Check (Highest Priority)
            $lowerLine = strtolower($line);
            foreach ($prefixes as $prefix) {
                if (strpos($lowerLine, $prefix) === 0) {
                    $potentialName = trim(substr($line, strlen($prefix)));
                    if (str_word_count($potentialName) >= 2 && !preg_match('/@/', $potentialName)) {
                        return $potentialName;
                    }
                }
            }

            // Stop guessing after first page-ish (40 lines)
            if ($index > 40) break;

            // Skip if it is the email or phone line
            if ($email && strpos($line, $email) !== false) continue;
            if ($phone && strpos($line, $phone) !== false) continue;

            // Cleanup line (Added underscore and more aggressive cleanup)
            $cleanLine = trim($line, " -|/,\t\n\r_");
            
            // Fix Spaced Out Names (e.g., "C O N N O R")
            // If the line consists of single letters separated by spaces, collapse them.
            if (preg_match('/^([A-Z]\s)+[A-Z]$/', $cleanLine)) {
                $collapsed = str_replace(' ', '', $cleanLine);
                // "CONNORHAMILTON" -> This loses the space between first/last name if it was "C O N N O R   H A M I L T O N"
                // Better approach: Collapse single letter sequences, but keep wider spaces?
                // For simplicity, let's just use the original line for now, but count it differently?
                // Actually, pure "C O N N O R" is hard to distinguish from random letters. 
                // Let's try to collapsing it and see if it looks like a name.
                // Assuming "C O N N O R  H A M I L T O N" usually has double spaces or just consistent single spaces.
                // Let's treat it as a strong candidate if it collapses to something clean.
                // A better REGEX for "Spaced Caps"
                 $score = 0;
                 $score += (40 - $index);
                 $score += 20; // Very stylistic, likely a header name
                 $candidates[] = ['text' => $cleanLine, 'score' => $score];
                 continue; // Don't process this line further
            }

            // Skip short/long lines
            if (strlen($cleanLine) < 2 || strlen($cleanLine) > 50) continue;

            // Skip if contains numbers (Names usually don't have numbers)
            if (preg_match('/[0-9]/', $cleanLine)) continue;

            // Check if line should be ignored
            if ($this->shouldIgnoreLine($cleanLine, $ignoreWords, $organizationKeywords, $designationKeywords)) {
                continue;
            }

            // Check for Merging with Next Line (Fix for "Isabel" \n "Schumacher")
            // If current line is 1 word, and next line is 1 word, and both look like names -> Merge
            $wordCount = str_word_count($cleanLine);
            $mergedName = null;
            
            if ($wordCount == 1 && $index + 1 < $count) {
                $nextLine = trim($lines[$index + 1], " -|/,\t\n\r");
                if (!empty($nextLine) && !preg_match('/[0-9]/', $nextLine)) {
                     // Check if next line is also valid name part
                     if (!$this->shouldIgnoreLine($nextLine, $ignoreWords, $organizationKeywords, $designationKeywords)) {
                         $nextWordCount = str_word_count($nextLine);
                         if ($nextWordCount == 1) {
                             // potential merge
                             $mergedName = $cleanLine . ' ' . $nextLine;
                         }
                     }
                }
            }

            // Candidates to score: either the single line, OR the merged line (if valid)
            // We prioritize the merged one if it exists and looks like Title Case
            $candidatesToScore = [];
            
            if ($mergedName) {
                 $candidatesToScore[] = $mergedName;
            }
            // Also consider the single line (in case it's just "Isabel" or "Connor Hamilton" as one line)
            $candidatesToScore[] = $cleanLine;

            foreach ($candidatesToScore as $textCandidate) {
                 $cWordCount = str_word_count($textCandidate);
                 
                 // Handle "C O N N O R H A M I L T O N" case where word count might be high due to spaces
                 // If there are many single characters, skip word count check
                 $singleChars = preg_match_all('/[A-Z]\b/', $textCandidate);
                 if ($singleChars > 3 && $cWordCount > 4) {
                     // This is likely a spaced out name that wasn't caught by the simplistic regex above
                     // Let's assume valid
                 } else {
                     // Normal Word Count Check: Min words 1, Max 4
                     if ($cWordCount < 1 || $cWordCount > 4) continue;
                 }

                 // Scoring
                 $score = 0;
                 
                 // Position: Higher is better (HEAVILY favored now for top lines)
                 // Lines 0-2 get massive boost because "Title is Name"
                 if ($index < 3) {
                     $score += 50; 
                     $score += (40 - $index) * 2;
                 } else {
                     $score += (40 - $index);
                 }

                 // Merged bonus?
                 if ($textCandidate === $mergedName) $score += 15;

                 // Capitalization
                 if (preg_match('/^[A-Z][a-z]+(\s+[A-Z][a-z]+)+$/', $textCandidate)) {
                    $score += 20; // Title Case "John Doe"
                 } elseif (preg_match('/^[A-Z][a-z]+$/', $textCandidate)) {
                    $score += 10; // Single Title Case "John"
                 } elseif (preg_match('/^[A-Z\s\.]+$/', $textCandidate)) {
                    $score += 15; // ALL CAPS or SPACED CAPS
                 } else {
                    $score -= 10;
                 }
                 
                 $candidates[] = ['text' => $textCandidate, 'score' => $score];
            }
        }


        // Add Filename as a Candidate
        if ($filename) {
            // Remove extension
            $nameFromFilename = pathinfo($filename, PATHINFO_FILENAME);
            // Replace separators with spaces
            $nameFromFilename = str_replace(['_', '-', '.'], ' ', $nameFromFilename);
            // Remove common words and symbols
            $nameFromFilename = preg_replace('/\((.*?)\)/', '', $nameFromFilename); // Remove items in brackets
            $nameFromFilename = preg_replace('/\b(resume|cv|pdf|doc|docx)\b/i', '', $nameFromFilename);
            $nameFromFilename = trim(preg_replace('/\s+/', ' ', $nameFromFilename));
            
            // Check if valid name style
            if (str_word_count($nameFromFilename) >= 1 && !preg_match('/[0-9]/', $nameFromFilename)) {
                 // Give it a decent fallback score (45), but let actual text win if it's really good (score > 50)
                 // But if text is weak or non-existent, filename wins.
                 $candidates[] = ['text' => ucwords($nameFromFilename), 'score' => 45];
            }
        }

        // Return best candidate
        if (!empty($candidates)) {
             usort($candidates, function($a, $b) {
                return $b['score'] - $a['score'];
            });
            $bestName = $candidates[0]['text'];
            
            // Final Cleanup: Remove common clutter words that might have slipped through
            $bestName = preg_replace('/\((.*?)\)/', '', $bestName); 
            $bestName = preg_replace('/\b(resume|cv|pdf|doc|docx)\b/i', '', $bestName);
            $bestName = trim(preg_replace('/\s+/', ' ', $bestName));
            
            return ucwords(strtolower($bestName));
        }

        return 'Candidate';
    }

    private function shouldIgnoreLine($line, $ignoreWords, $orgKeywords, $designationKeywords) {
        // Check ignore words
        foreach ($ignoreWords as $word) {
            if (stripos($line, $word) !== false) return true;
        }
        // Check Org keywords
        foreach ($orgKeywords as $word) {
            if (stripos($line, $word) !== false) return true;
        }
        // Check Designation keywords
        foreach ($designationKeywords as $word) {
            if (stripos($line, $word) !== false) return true;
        }
    }

    public function getTests()
    {
        return response()->json(Test::all());
    }

    public function sendAssessment(Request $request, Candidate $candidate)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
        ]);

        $test = Test::findOrFail($request->test_id);
        $token = Str::random(32);

        // Create Assessment Record
        $assessment = CandidateAssessment::create([
            'candidate_id' => $candidate->id,
            'test_id' => $test->id,
            'status' => 'Sent',
            'sent_at' => now(),
            'token' => $token
        ]);

        // Generate Dynamic Link
        $uploadLink = route('assessment.show', ['token' => $token]);

        // Personalize Greeting and Replace Link
        $emailContent = str_replace('Dear Candidate', 'Dear ' . $candidate->name, $test->content);
        
        // Remove the placeholder from content as it will be in the CTA button instead
        $emailContent = str_replace('[Insert Upload Link Here]', '', $emailContent);

        // Send Email
        Mail::to($candidate->email)
            ->send(new AssessmentTaskMail($test->subject, $emailContent, auth()->user()->email, $uploadLink, $test->attachment_path));

        // Update Candidate Status
        $candidate->update([
            'status' => 'Assessment Sent',
            'stage' => 'test_sent'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assessment sent successfully'
        ]);
    }

    public function sendRejection(Request $request, Candidate $candidate)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isHR()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // Send Email
        $message = $request->input('rejection_message');
        Mail::to($candidate->email)
            ->send(new CandidateRejectionMail($candidate->name, $message));

        // Update Candidate Status
        $candidate->update([
            'status' => 'Rejected',
            'stage' => 'rejected',
            'finalized_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rejection email sent successfully'
        ]);
    }
    public function scheduleInterview(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'scheduled_at' => 'required|date',
            'duration' => 'required|integer',
            'interviewer_ids' => 'required|array',
            'interviewer_ids.*' => 'exists:users,id',
        ]);

        $candidate = Candidate::findOrFail($request->candidate_id);
        $interviewerIds = $request->interviewer_ids;
        $interviewers = User::whereIn('id', $interviewerIds)->get();
        
        $scheduledAt = Carbon::parse($request->scheduled_at);
        $endTime = $scheduledAt->copy()->addMinutes((int) $request->duration);

        // Attendees
        $attendees = [];
        // Add Candidate
        $attendees[] = ['email' => $candidate->email];
        
        // Add Interviewers
        foreach ($interviewers as $interviewer) {
            $attendees[] = ['email' => $interviewer->email];
        }

        // Add HR Email (from SMTP config)
        $hrEmail = config('mail.from.address');
        if ($hrEmail) {
            $attendees[] = ['email' => $hrEmail];
        }

        // Add Current User (Organizer) if not already in list
        $currentUserEmail = auth()->user()->email;
        $isOrganizerIncluded = false;
        foreach ($attendees as $attendee) {
            if ($attendee['email'] === $currentUserEmail) {
                $isOrganizerIncluded = true;
                break;
            }
        }
        if (!$isOrganizerIncluded) {
            $attendees[] = ['email' => $currentUserEmail];
        }

        // Add Guests
        $guests = array_map('trim', explode(',', $request->additional_guests ?? ''));
        foreach ($guests as $guest) {
            if (filter_var($guest, FILTER_VALIDATE_EMAIL)) {
                $attendees[] = ['email' => $guest, 'optional' => true];
            }
        }

        $googleService = new GoogleCalendarService();
        
        try {
            $resumeLink = $candidate->cv_path ? asset(Storage::url($candidate->cv_path)) : 'Not available';
            $designationName = $candidate->designation->name ?? $candidate->designation ?? 'Potential Hire';
            
            $interviewerNames = $interviewers->pluck('name')->implode(', ');
            
            $summary = "Interview: " . $candidate->name . " - " . $designationName;
            
            $customMessage = $request->input('custom_message');
            $customMessagePart = $customMessage ? $customMessage . "\n\n--------------------------\n\n" : "";

            $description = $customMessagePart . 
                          "Dear {$candidate->name},\n\n" .
                          "We are pleased to invite you to an interview for the above mentioned vacancy ({$designationName}) at Loops Integrated. Please find the link to the office location below.\n\n" .
                          "https://maps.app.goo.gl/X9Z2b3xaEZS4FTr7A\n\n" .
                          "Should you have any questions or concerns, please do not hesitate to contact me.\n\n" .
                          "Thank you, and we look forward to meeting you.\n\n\n" .
                          "Regards,\n" .
                          "Loops HR\n\n" .
                          "+94757667686 | +94112081689\n" .
                          "www.loopsintegrated.com\n" .
                          "2B, Sulaiman Terrace, Colombo 05,\n" .
                          "Sri Lanka.\n\n" .
                          "--------------------------\n" .
                          "Interviewer reference:\n" .
                          "Interviewers: {$interviewerNames}\n" .
                          "Resume Link: " . $resumeLink;

            $eventData = $googleService->createMeetEvent([
                'summary' => $summary,
                'description' => $description,
                'start_time' => $scheduledAt->toIso8601String(),
                'end_time' => $endTime->toIso8601String(),
                'attendees' => $attendees,
            ]);

            $interview = Interview::create([
                'candidate_id' => $candidate->id,
                'hod_id' => $interviewerIds[0], // Primary interviewer
                'scheduled_at' => $scheduledAt,
                'duration' => $request->duration,
                'meet_link' => $eventData['hangoutLink'],
                'google_event_id' => $eventData['eventId'],
                'additional_guests' => $request->additional_guests,
            ]);

            // Sync all interviewers to pivot table
            $interview->interviewers()->sync($interviewerIds);

            // Update candidate stage ONLY if they haven't reached interview stage yet
        $earlyStages = ['default', 'shortlisted', 'test_sent', 'test_received'];
        if (in_array($candidate->stage, $earlyStages)) {
            $candidate->update([
                'stage' => '1st_interview', 
                'status' => 'Interview Scheduled'
            ]);
        }

            // Note: Manual email sending is disabled because Google Calendar sends the "proper" invitation
            // with RSVP buttons when 'sendUpdates' is set to 'all' in GoogleCalendarService.
            // This prevents candidates from receiving duplicate emails.

            return redirect()->back()->with('success', "Interview scheduled! Proper Google invitation sent to all participants.");

        } catch (\Exception $e) {
            
            return redirect()->back()->with('error', 'Failed to schedule: ' . $e->getMessage());
        }
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'hod_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $hod = User::findOrFail($request->hod_id);
        
        // Define range: The whole day selected (00:00 to 23:59)
        $date = Carbon::parse($request->date); // Local time
        $startOfDay = $date->copy()->startOfDay()->toIso8601String();
        $endOfDay = $date->copy()->endOfDay()->toIso8601String();

        $googleService = new GoogleCalendarService(); 

        if (!$googleService->isConnected()) {
             return response()->json(['error' => 'Google Calendar not connected'], 500);
        }

        try {
            $result = $googleService->getHODEvents($hod->email, $startOfDay, $endOfDay);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Availability Check Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to check availability: ' . $e->getMessage()], 500);
        }
    }

    public function updateDefaultRejectionTemplate(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isHR()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        \App\Models\RejectionTemplate::updateOrCreate(
            ['type' => 'default'],
            ['content' => $request->message]
        );

        return response()->json(['success' => true, 'message' => 'Template updated successfully']);
    }
}
