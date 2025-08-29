<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\HomepageFeatureController;
use App\Http\Controllers\Admin\ClientController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminController::class, 'login'])->name('login');
    Route::post('login', [AdminController::class, 'authenticate'])->name('login.post');
    Route::post('logout', [AdminController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')
    ->middleware(\App\Http\Middleware\JWTAuthMiddleware::class)
    ->group(function () {
    
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    Route::resource('services', ServiceController::class);
    Route::resource('leads', LeadController::class)->except(['create', 'store']);
    Route::resource('articles', ArticleController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('industries', IndustryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('clients', ClientController::class);
    
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/', [HomepageFeatureController::class, 'index'])->name('index');
        Route::get('create', [HomepageFeatureController::class, 'create'])->name('create');
        Route::post('/', [HomepageFeatureController::class, 'store'])->name('store');
        Route::get('{content}', [HomepageFeatureController::class, 'show'])->name('show');
        Route::get('{content}/edit', [HomepageFeatureController::class, 'edit'])->name('edit');
        Route::put('{content}', [HomepageFeatureController::class, 'update'])->name('update');
        Route::delete('{content}', [HomepageFeatureController::class, 'destroy'])->name('destroy');
    });
});