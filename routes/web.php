<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

Route::get('/dashboard', function () {
    $googleService = new \App\Services\GoogleCalendarService();
    $isCalendarConnected = $googleService->isConnected();
    return view('dashboard', compact('isCalendarConnected'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/system/maintenance/toggle', [\App\Http\Controllers\MaintenanceController::class, 'toggle'])->name('maintenance.toggle');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/recruitment', [\App\Http\Controllers\RecruitmentController::class, 'index'])->name('recruitment.index');
    Route::get('/recruitment/department/{department}', [\App\Http\Controllers\RecruitmentController::class, 'department'])->name('recruitment.department');
    Route::post('/recruitment/department', [\App\Http\Controllers\RecruitmentController::class, 'storeDepartment'])->name('recruitment.storeDepartment');
    Route::patch('/recruitment/department/{department}', [\App\Http\Controllers\RecruitmentController::class, 'updateDepartment'])->name('recruitment.updateDepartment');
    Route::delete('/recruitment/department/{department}', [\App\Http\Controllers\RecruitmentController::class, 'destroyDepartment'])->name('recruitment.destroyDepartment');
    Route::get('/recruitment/department/{department}/designation/{designation}', [\App\Http\Controllers\RecruitmentController::class, 'designation'])->name('recruitment.designation');
    Route::post('/recruitment/department/{department}/designation', [\App\Http\Controllers\RecruitmentController::class, 'storeDesignation'])->name('recruitment.storeDesignation');
    Route::delete('/recruitment/designation/{designation}', [\App\Http\Controllers\RecruitmentController::class, 'destroyDesignation'])->name('recruitment.destroyDesignation');
    Route::patch('/recruitment/designation/{designation}', [\App\Http\Controllers\RecruitmentController::class, 'updateDesignation'])->name('recruitment.updateDesignation');
    Route::patch('/recruitment/designation/{designation}/toggle-status', [\App\Http\Controllers\RecruitmentController::class, 'toggleDesignationStatus'])->name('recruitment.toggleDesignationStatus');
    Route::get('/recruitment/create', [\App\Http\Controllers\RecruitmentController::class, 'create'])->name('recruitment.create');
    Route::post('/recruitment/preview-cv', [\App\Http\Controllers\RecruitmentController::class, 'previewCV'])->name('recruitment.previewCV');
    Route::post('/recruitment', [App\Http\Controllers\RecruitmentController::class, 'store'])->name('recruitment.store');
    Route::post('/recruitment/bulk', [App\Http\Controllers\RecruitmentController::class, 'bulkStore'])->name('recruitment.bulkStore');
    Route::patch('/recruitment/candidate/{candidate}', [\App\Http\Controllers\RecruitmentController::class, 'updateCandidate'])->name('recruitment.updateCandidate');
    Route::get('/recruitment/candidate/{candidate}/feedbacks', [\App\Http\Controllers\RecruitmentController::class, 'getFeedbacks'])->name('recruitment.getFeedbacks');
    Route::post('/recruitment/candidate/{candidate}/feedbacks', [\App\Http\Controllers\RecruitmentController::class, 'storeFeedback'])->name('recruitment.storeFeedback');
    Route::patch('/recruitment/feedbacks/{feedback}', [\App\Http\Controllers\RecruitmentController::class, 'updateFeedback'])->name('recruitment.updateFeedback');
    Route::delete('/recruitment/feedbacks/{feedback}', [\App\Http\Controllers\RecruitmentController::class, 'destroyFeedback'])->name('recruitment.destroyFeedback');
    Route::post('/recruitment/candidate/{candidate}/archive', [\App\Http\Controllers\RecruitmentController::class, 'archiveCandidate'])->name('recruitment.archiveCandidate');
    Route::post('/recruitment/candidate/{candidate}/unarchive', [\App\Http\Controllers\RecruitmentController::class, 'unarchiveCandidate'])->name('recruitment.unarchiveCandidate');
    Route::post('/recruitment/candidates/bulk-archive', [\App\Http\Controllers\RecruitmentController::class, 'bulkArchive'])->name('recruitment.bulkArchive');
    Route::post('/recruitment/candidates/bulk-unarchive', [\App\Http\Controllers\RecruitmentController::class, 'bulkUnarchive'])->name('recruitment.bulkUnarchive');
    Route::post('/recruitment/candidates/bulk-destroy', [\App\Http\Controllers\RecruitmentController::class, 'bulkDestroy'])->name('recruitment.bulkDestroy');

    // Assessment Features
    Route::get('/recruitment/tests-data', [\App\Http\Controllers\RecruitmentController::class, 'getTests'])->name('recruitment.tests.get');
    Route::post('/recruitment/candidate/{candidate}/send-assessment', [\App\Http\Controllers\RecruitmentController::class, 'sendAssessment'])->name('recruitment.candidate.sendAssessment');
    Route::post('/recruitment/candidate/{candidate}/send-rejection', [\App\Http\Controllers\RecruitmentController::class, 'sendRejection'])->name('recruitment.candidate.sendRejection');
    Route::post('/recruitment/update-default-rejection', [\App\Http\Controllers\RecruitmentController::class, 'updateDefaultRejectionTemplate'])->name('recruitment.rejection.updateDefault');
    Route::resource('/recruitment/tests', \App\Http\Controllers\TestController::class)->names('tests');

    Route::middleware('admin')->group(function () {
        Route::get('/system/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('/system/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::patch('/system/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/system/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });

    // Interview Scheduling
    Route::post('/recruitment/interview', [\App\Http\Controllers\RecruitmentController::class, 'scheduleInterview'])->name('recruitment.scheduleInterview');
    Route::post('/recruitment/check-availability', [\App\Http\Controllers\RecruitmentController::class, 'checkAvailability'])->name('recruitment.checkAvailability');
    
    // Google Calendar OAuth
    Route::get('/google/calendar/redirect', [\App\Http\Controllers\GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
    Route::get('/google/calendar/callback', [\App\Http\Controllers\GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');
    Route::post('/google/calendar/disconnect', [\App\Http\Controllers\GoogleCalendarController::class, 'disconnect'])->name('google.calendar.disconnect');
    Route::get('/google/calendar/status', [\App\Http\Controllers\GoogleCalendarController::class, 'status'])->name('google.calendar.status');
    // HR Analytics
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [\App\Http\Controllers\AnalyticsController::class, 'export'])->name('analytics.export');
    
    Route::post('/notifications/{id}/mark-as-read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markAsRead');

    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markAllRead');
    
    // Notification polling endpoint
    Route::get('/api/notifications/check', function () {
        $user = auth()->user();
        $unreadCount = $user->unreadNotifications->count();
        $notifications = $user->unreadNotifications->take(5)->map(function($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? 'New Notification',
                'designation' => $n->data['designation'] ?? null,
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
            ];
        });
        
        return response()->json([
            'count' => $unreadCount,
            'notifications' => $notifications
        ]);
    })->name('notifications.check');
});

require __DIR__.'/auth.php';

// Public Assessment Portal
Route::get('/assessment/{token}', [\App\Http\Controllers\AssessmentController::class, 'show'])->name('assessment.show');
Route::post('/assessment/{token}/submit', [\App\Http\Controllers\AssessmentController::class, 'submit'])->name('assessment.submit');

// Diagnostic FTP Test
Route::get('/test-ftp', function () {
    try {
        $disk = \Illuminate\Support\Facades\Storage::disk('ftp_cvs');
        $filename = 'test_connection_' . time() . '.txt';
        $content = 'FTP Connection Test Successful at ' . now()->toDateTimeString();
        
        $path = env('FTP_CV_FOLDER', 'cvs');
        if ($path === '.' || empty($path)) {
            $fullPath = $filename;
        } else {
            $fullPath = rtrim($path, '/') . '/' . $filename;
        }

        echo "<h3>FTP Diagnostics</h3>";
        echo "<b>Host:</b> " . env('FTP_HOST') . "<br>";
        echo "<b>Username:</b> " . env('FTP_USERNAME') . "<br>";
        echo "<b>Root:</b> " . env('FTP_ROOT') . "<br>";
        echo "<b>Target Path:</b> " . $fullPath . "<br><br>";

        echo "Attempting to write file... ";
        $success = $disk->put($fullPath, $content);

        if ($success) {
            echo "<span style='color: green;'>SUCCESS!</span><br>";
            echo "<b>Public URL (derived):</b> <a href='" . config('filesystems.disks.ftp_cvs.url') . "/" . $fullPath . "' target='_blank'>" . config('filesystems.disks.ftp_cvs.url') . "/" . $fullPath . "</a><br>";
            
            echo "<br>Listing files in root:<br>";
            print_r($disk->files('/'));
        } else {
            echo "<span style='color: red;'>FAILED (Returned false)</span>";
        }

    } catch (\Exception $e) {
        echo "<span style='color: red;'>ERROR:</span> " . $e->getMessage();
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});
