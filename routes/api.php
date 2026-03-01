<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\AiProviderController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/subjects', [SubjectController::class, 'store']);
    Route::get('/subjects/{id}/outcomes', [SubjectController::class, 'outcomes']);
    Route::post('/subjects/{id}/outcomes', [SubjectController::class, 'storeOutcome']);

    // Questions
    Route::post('/questions/upload', [QuestionController::class, 'upload']);
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/questions/{id}', [QuestionController::class, 'show']);
    Route::put('/questions/{id}', [QuestionController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);

    // Tests
    Route::post('/tests/generate-selected', [TestController::class, 'generateFromSelected']);
    Route::post('/tests/generate', [TestController::class, 'generate']);
    Route::get('/tests', [TestController::class, 'index']);
    Route::get('/tests/{id}', [TestController::class, 'show']);
    Route::delete('/tests/{id}', [TestController::class, 'destroy']);
    Route::post('/tests/{id}/submit', [TestController::class, 'submit']);
    
    // AI Providers
    Route::prefix('ai-providers')->group(function () {
        Route::get('/', [AiProviderController::class, 'index']);
        Route::put('/{id}', [AiProviderController::class, 'update']);
        Route::post('/{id}/set-active', [AiProviderController::class, 'setActive']);
    });
});
