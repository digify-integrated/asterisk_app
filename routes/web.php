<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AppRenderController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Middleware\MenuReadMiddleware;
use App\Http\Middleware\ShareNavigationData;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('apps.main')
        : view('auth.login', [
            'pageTitle'   => config('app.name'),
            'title'       => 'Welcome back',
            'description' => 'Log in to your account',
        ]);
})->name('login');


Route::middleware('guest')->group(function () {
    Route::post('/auth/authenticate', [AuthenticationController::class, 'authenticate'])->name('authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/app', [AppRenderController::class, 'index'])->name('apps.main');
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

    Route::prefix('app')
        ->name('app.')
        ->controller(AppController::class)
        ->group(function () {
            Route::post('/save', 'save')->name('save');
            Route::post('/delete', 'delete')->name('delete');
            Route::post('/delete-multiple', 'deleteMultiple')->name('delete.multiple');
            Route::get('/fetch-details', 'fetchDetails')->name('fetch.details');
            Route::get('/generate-table', 'generateTable')->name('generate.table');
            Route::get('/generate-options', 'generateOptions')->name('generate.options');
        });

    Route::prefix('audit-log')
        ->name('audit-log.')
        ->controller(AuditLogController::class)
        ->group(function () {
            Route::get('/fetch', 'fetch')->name('fetch');
        });

    Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout');
});