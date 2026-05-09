<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/submissions/{submission}', [SubmissionController::class, 'show']);
    Route::patch('/submissions/{submission}', [SubmissionController::class, 'update']);
    Route::post('/submissions/{submission}/submit', [SubmissionController::class, 'submit']);
    Route::post('/supervisor/submissions/{submission}/review', [SubmissionController::class, 'supervisorReview']);
});
