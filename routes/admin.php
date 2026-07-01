<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'storeLogin']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'storeRegister']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');

    Route::get('/branding', [BrandingController::class, 'index'])->name('branding.index');
    Route::put('/branding/{brand}', [BrandingController::class, 'update'])->name('branding.update');

    Route::resource('roles', RoleController::class);

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity_logs.show');

    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/business/branding', [App\Http\Controllers\Admin\BusinessSettingController::class, 'index'])->name('business.branding');
    Route::post('/business/branding', [App\Http\Controllers\Admin\BusinessSettingController::class, 'update'])->name('business.update');

    Route::get('/business/legal', [App\Http\Controllers\Admin\LegalController::class, 'index'])->name('legal.index');
    Route::post('/business/legal', [App\Http\Controllers\Admin\LegalController::class, 'update'])->name('legal.update');

    Route::get('/business/settings', [App\Http\Controllers\Admin\BusinessLogicController::class, 'index'])->name('business.settings.index');
    Route::post('/business/settings', [App\Http\Controllers\Admin\BusinessLogicController::class, 'update'])->name('business.settings.update');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [App\Http\Controllers\Admin\NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Documentation Management
    Route::prefix('docs')->name('docs.')->group(function () {
        Route::resource('projects', \App\Http\Controllers\Admin\Docs\DocProjectController::class);
        Route::resource('versions', \App\Http\Controllers\Admin\Docs\DocVersionController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\Docs\DocCategoryController::class);
        Route::resource('pages', \App\Http\Controllers\Admin\Docs\DocPageController::class);
        Route::get('pages/{page}/revisions', [\App\Http\Controllers\Admin\Docs\DocPageController::class, 'revisions'])->name('pages.revisions');
        Route::post('revisions/{revision}/restore', [\App\Http\Controllers\Admin\Docs\DocPageController::class, 'restoreRevision'])->name('revisions.restore');
        Route::post('upload-image', \App\Http\Controllers\Admin\Docs\DocImageController::class)->name('upload.image');
    });
});
