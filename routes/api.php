<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\IndustryController;
use App\Http\Controllers\API\LeadController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\HomepageFeatureController;

// Authentication routes (no middleware)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Protected routes with Sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', [AuthController::class, 'user']);
    
    // Resource routes for authenticated users
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('articles', ArticleController::class);
    Route::apiResource('industries', IndustryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('homepage-features', HomepageFeatureController::class);
    
    // Leads - only index and store for authenticated users
    Route::get('leads', [LeadController::class, 'index']);
    Route::get('leads/{lead}', [LeadController::class, 'show']);
    Route::delete('leads/{lead}', [LeadController::class, 'destroy']);
});

// Public routes (no authentication required)
Route::post('leads', [LeadController::class, 'store']); // Public contact form submission

// Public read-only routes
Route::get('services/public', [ServiceController::class, 'index']); // Public services list
Route::get('projects/public', [ProjectController::class, 'index']); // Public projects list
Route::get('articles/public', [ArticleController::class, 'index']); // Public articles list
Route::get('industries/public', [IndustryController::class, 'index']); // Public industries list
Route::get('products/public', [ProductController::class, 'index']); // Public products list
Route::get('homepage-features/public', [HomepageFeatureController::class, 'index']); // Public homepage features
