<?php

use Illuminate\Support\Facades\Route;
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
    return response()->json(['message' => 'Havor API is running']);
});


Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/authenticate', [AdminController::class, 'authenticate'])->name('admin.authenticate');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');
Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
Route::post('/admin/services', [ServiceController::class, 'store'])->name('admin.services.store');
Route::get('/admin/services/{service}', [ServiceController::class, 'show'])->name('admin.services.show');
Route::get('/admin/services/{service}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
Route::put('/admin/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
Route::delete('/admin/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');


Route::get('/admin/leads', [LeadController::class, 'index'])->name('admin.leads.index');
Route::get('/admin/leads/{lead}', [LeadController::class, 'show'])->name('admin.leads.show');
Route::get('/admin/leads/{lead}/edit', [LeadController::class, 'edit'])->name('admin.leads.edit');
Route::put('/admin/leads/{lead}', [LeadController::class, 'update'])->name('admin.leads.update');
Route::delete('/admin/leads/{lead}', [LeadController::class, 'destroy'])->name('admin.leads.destroy');

Route::get('/admin/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
Route::get('/admin/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
Route::post('/admin/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
Route::get('/admin/articles/{article}', [ArticleController::class, 'show'])->name('admin.articles.show');
Route::get('/admin/articles/{article}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
Route::put('/admin/articles/{article}', [ArticleController::class, 'update'])->name('admin.articles.update');
Route::delete('/admin/articles/{article}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

Route::get('/admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
Route::get('/admin/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
Route::post('/admin/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
Route::get('/admin/projects/{project}', [ProjectController::class, 'show'])->name('admin.projects.show');
Route::get('/admin/projects/{project}/edit', [ProjectController::class, 'edit'])->name('admin.projects.edit');
Route::put('/admin/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
Route::delete('/admin/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');

Route::get('/admin/industries', [IndustryController::class, 'index'])->name('admin.industries.index');
Route::get('/admin/industries/create', [IndustryController::class, 'create'])->name('admin.industries.create');
Route::post('/admin/industries', [IndustryController::class, 'store'])->name('admin.industries.store');
Route::get('/admin/industries/{industry}', [IndustryController::class, 'show'])->name('admin.industries.show');
Route::get('/admin/industries/{industry}/edit', [IndustryController::class, 'edit'])->name('admin.industries.edit');
Route::put('/admin/industries/{industry}', [IndustryController::class, 'update'])->name('admin.industries.update');
Route::delete('/admin/industries/{industry}', [IndustryController::class, 'destroy'])->name('admin.industries.destroy');

Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
Route::get('/admin/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

Route::get('/admin/content', [HomepageFeatureController::class, 'index'])->name('admin.content.index');
Route::get('/admin/content/create', [HomepageFeatureController::class, 'create'])->name('admin.content.create');
Route::post('/admin/content', [HomepageFeatureController::class, 'store'])->name('admin.content.store');
Route::get('/admin/content/{content}', [HomepageFeatureController::class, 'show'])->name('admin.content.show');
Route::get('/admin/content/{content}/edit', [HomepageFeatureController::class, 'edit'])->name('admin.content.edit');
Route::put('/admin/content/{content}', [HomepageFeatureController::class, 'update'])->name('admin.content.update');
Route::delete('/admin/content/{content}', [HomepageFeatureController::class, 'destroy'])->name('admin.content.destroy');

Route::get('/admin/clients', [ClientController::class, 'index'])->name('admin.clients.index');
Route::get('/admin/clients/create', [ClientController::class, 'create'])->name('admin.clients.create');
Route::post('/admin/clients', [ClientController::class, 'store'])->name('admin.clients.store');
Route::get('/admin/clients/{client}', [ClientController::class, 'show'])->name('admin.clients.show');
Route::get('/admin/clients/{client}/edit', [ClientController::class, 'edit'])->name('admin.clients.edit');
Route::put('/admin/clients/{client}', [ClientController::class, 'update'])->name('admin.clients.update');
Route::delete('/admin/clients/{client}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');

Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
Route::get('/admin/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');