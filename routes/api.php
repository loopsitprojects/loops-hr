<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CandidateApiController;
use App\Http\Controllers\CandidateWebhookController;
use App\Http\Middleware\EnsureApiTokenIsValid;

// WPForms Webhook (no auth required, external webhook)
Route::post('/webhook/wpforms', [CandidateWebhookController::class, 'handleWPFormsWebhook']);

Route::middleware([EnsureApiTokenIsValid::class])->group(function () {
    Route::post('/candidates', [CandidateApiController::class, 'store']);
});
