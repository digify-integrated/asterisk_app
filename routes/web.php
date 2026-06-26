<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/', 'auth.login', [
        'pageTitle' => config('app.name', 'Laravel'),
        'title' => 'Welcome back',
        'description' => 'Log in to your account'
    ])->name('login');

    Route::post('/auth/authenticate', [AuthenticationController::class, 'authenticate'])->name('authenticate');
});

Route::middleware('auth')->group(function () {
    Route::view('/app', 'apps.index', [
        'pageTitle' => 'Apps',
    ])->name('apps.index');

    Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout');
});