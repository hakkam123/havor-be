<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Havor API is running']);
});

Route::get('/test', function () {
    return 'Laravel is working!';
});

Route::get('/admin/login', function () {
    return view('admin.login');
});
