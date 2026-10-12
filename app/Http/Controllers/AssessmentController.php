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
        
        if ($assessment->status === 'Submitted') {
            return redirect()->back()->with('success', 'Your assessment has already been submitted and is under review.');
        }

        $rawUrl = trim($request->input('submission_links', ''));

        // Normalize URL if scheme is missing
        if ($rawUrl !== '' && !preg_match('/^https?:\/\//i', $rawUrl)) {
            $rawUrl = 'https://' . $rawUrl;
            $request->merge(['submission_links' => $rawUrl]);
        }

        $request->validate([
            'submission_links' => [
                'required',
                'string',
                'url',
                'max:1000',
                function ($attribute, $value, $fail) {
                    $parsed = parse_url($value);
                    $host = strtolower($parsed['host'] ?? '');
                    $validHosts = ['drive.google.com', 'docs.google.com'];
                    if (!in_array($host, $validHosts)) {
                        $fail('The submission link must be a valid Google Drive link (e.g., https://drive.google.com/...). Other links are not allowed.');
                        return;
                    }
                    $path = $parsed['path'] ?? '';
                    $query = $parsed['query'] ?? '';
                    if (($path === '' || $path === '/') && empty($query)) {
                        $fail('Please provide the complete Google Drive link to your specific file or folder.');
                    }
                },
            ],
        ], [
            'submission_links.required' => 'Please provide your Google Drive submission link.',
            'submission_links.url' => 'Please provide a valid URL.',
        ]);

        $assessment->update([
            'status' => 'Submitted',
            'submission_link' => $rawUrl,
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
