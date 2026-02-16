<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Designation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use App\Models\User;
use App\Notifications\NewCandidateApplication;
use Illuminate\Support\Facades\Notification;

class CandidateApiController extends Controller
{
    public function store(Request $request)
    {
        Log::info('API Candidate Submission:', $request->except('cv'));

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'designation_id' => 'required|exists:designations,id',
            'expected_salary' => 'nullable|string|max:255',
            'cv' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('cv');
            $path = $file->store('cvs', 'public');
            
            // Fetch designation name for the redundant 'designation' string column
            // We need this because currently the system uses both ID and string name
            $designation = Designation::find($request->designation_id);
            if (!$designation) {
                return response()->json(['message' => 'Invalid designation ID'], 422);
            }

            $candidate = Candidate::create([
                'department_id' => $designation->department_id, // Auto-link to department
                'designation_id' => $designation->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'expected_salary' => $request->expected_salary,
                'designation' => $designation->name, // Legacy string column support
                'cv_path' => $path,
                'stage' => 'test', 
                'status' => 'pending', 
                'status' => 'pending', 
                'hod_comment' => null,
            ]);

            // Notify HR Admin and Super Admin
            $recipients = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_HR_ADMIN])->get();
            Notification::send($recipients, new NewCandidateApplication($candidate));

            return response()->json([
                'success' => true,
                'message' => 'Candidate application submitted successfully.',
                'candidate_id' => $candidate->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Candidate Submission Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
