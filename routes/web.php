<?php

use App\Http\Controllers\AppRenderController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Middleware\MenuReadMiddleware;
use App\Http\Middleware\ShareNavigationData;
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
    Route::get('/app', [AppRenderController::class, 'index'])->name('apps.main');

    Route::middleware([MenuReadMiddleware::class, ShareNavigationData::class])->group(function () {
        Route::get('/app/{appId}/module/{navigationMenuId}', [AppRenderController::class, 'renderModule'])
            ->defaults('route_type', 'index')
            ->name('apps.base');
            
        Route::get('/app/{appId}/module/{navigationMenuId}/manage', [AppRenderController::class, 'renderModule'])
            ->defaults('route_type', 'manage')
            ->name('apps.manage');
            
        Route::get('/app/{appId}/module/{navigationMenuId}/import', [AppRenderController::class, 'renderModule'])
            ->defaults('route_type', 'import')
            ->name('apps.import');
    });

    Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout');
});