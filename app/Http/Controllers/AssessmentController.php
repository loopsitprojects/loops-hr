<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CandidateAssessment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Notifications\AssessmentSubmitted;
use Illuminate\Support\Facades\Notification;

class AssessmentController extends Controller
{
    public function show($token)
    {
        $assessment = CandidateAssessment::with(['candidate', 'test'])->where('token', $token)->firstOrFail();
        return view('assessment.show', compact('assessment'));
    }

    public function submit(Request $request, $token)
    {
        $assessment = CandidateAssessment::with(['candidate', 'test'])->where('token', $token)->firstOrFail();
        
        $request->validate([
            'submission_links' => 'required|string|max:1000',
        ]);

        $assessment->update([
            'status' => 'Submitted',
            'submission_link' => $request->submission_links,
            'file_path' => null
        ]);

        // Notify HR Admin and Super Admin
        try {
            $recipients = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_HR_ADMIN])->get();
            if ($recipients->count() > 0) {
                Notification::send($recipients, new AssessmentSubmitted($assessment));
                Log::info("Assessment notification sent for candidate: " . $assessment->candidate->name);
            }
        } catch (\Exception $e) {
            Log::error("Notification error: " . $e->getMessage());
        }
        
        // Update candidate status
        $assessment->candidate->update([
            'status' => 'Assessment Submitted',
            'stage' => 'test_received'
        ]);

        return redirect()->back()->with('success', 'Your assessment has been submitted successfully.');
    }
}
