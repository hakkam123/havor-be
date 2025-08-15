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
use App\Http\Controllers\API\ClientController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', [AuthController::class, 'user']);
    
//     // Resource routes for authenticated users
//     Route::apiResource('services', ServiceController::class);
//     Route::apiResource('projects', ProjectController::class);
//     Route::apiResource('articles', ArticleController::class);
//     Route::apiResource('industries', IndustryController::class);
//     Route::apiResource('products', ProductController::class);
//     Route::apiResource('homepage-features', HomepageFeatureController::class);
    
//     // Leads - only index and store for authenticated users
//     Route::get('leads', [LeadController::class, 'index']);
//     Route::get('leads/{lead}', [LeadController::class, 'show']);
//     Route::delete('leads/{lead}', [LeadController::class, 'destroy']);
// });

Route::post('leads', [LeadController::class, 'store']);
Route::apiResource('clients/public', ClientController::class);
Route::apiResource('articles/public', ArticleController::class);
Route::apiResource('projects/public', ProjectController::class);
Route::apiResource('services/public', ServiceController::class);

Route::get('services/public/{id}/articles', [ServiceController::class, 'articles']);
Route::get('services/public/{id}/projects', [ServiceController::class, 'projects']);
