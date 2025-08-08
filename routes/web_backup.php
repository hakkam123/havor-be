<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\HomepageFeatureController;
use App\Http\Controllers\Admin\LeadController;

Route::get('/', function () {
    return response()->json(['message' => 'Havor API is running']);
});

Route::get('/test', function () {
    return 'Laravel is working!';
});

Route::get('/admin-test', function () {
    return 'Admin test working!';
});

Route::get('/admin/test-simple', function () {
    return 'Simple admin route working!';
});

Route::get('/admin/login-simple', function () {
    return view('admin.login');
});

// Admin Routes - Step by step
Route::prefix('admin')->name('admin.')->group(function () {
    // Login routes (no auth middleware)
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/authenticate', [AdminController::class, 'authenticate'])->name('authenticate');
    
    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});

/*
Route::get('/admin/simple-login', function () {
    return 'Simple admin login working!';
});

// Admin Routes - Simple Version
Route::prefix('admin')->name('admin.')->group(function () {
    // Login routes (no auth middleware)
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/authenticate', [AdminController::class, 'authenticate'])->name('authenticate');
    
    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        
        // Resource routes
        Route::resource('services', ServiceController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('articles', ArticleController::class);
        Route::resource('industries', IndustryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('homepage-features', HomepageFeatureController::class);
        
        // Leads routes (no create/store since leads come from contact forms)
        Route::resource('leads', LeadController::class)->except(['create', 'store']);
        Route::post('leads/bulk-update', [LeadController::class, 'bulkUpdate'])->name('leads.bulk-update');
    });
});
*/
