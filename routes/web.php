<?php

use App\Http\Controllers\Guest\AuthController;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Guest\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
})->name('login');

Route::middleware('guest')->group(function () {
    // Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    // Route::post('login', [AuthController::class, 'storeLogin']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'storeRegister']);
});

Route::post('logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/privacy-policy', [App\Http\Controllers\Guest\LegalPageController::class, 'show'])->defaults('slug', 'privacy-policy')->name('legal.privacy');
Route::get('/terms-and-conditions', [App\Http\Controllers\Guest\LegalPageController::class, 'show'])->defaults('slug', 'terms-and-conditions')->name('legal.terms');
Route::get('/about-us', [App\Http\Controllers\Guest\LegalPageController::class, 'show'])->defaults('slug', 'about-us')->name('legal.about');
Route::get('/contact-us', [App\Http\Controllers\Guest\ContactController::class, 'show'])->name('contact');

// Dashboard fallback or redirect based on role could be handled here if needed.

Route::prefix('docs')->name('docs.')->group(function () {
    Route::get('{projectSlug}/{versionSlug}/search', [\App\Http\Controllers\Public\DocsController::class, 'search'])->name('search');
    Route::get('{projectSlug}', [\App\Http\Controllers\Public\DocsController::class, 'show'])->name('show.initial');
    Route::get('{projectSlug}/{versionSlug}/{categorySlug}/{pageSlug}', [\App\Http\Controllers\Public\DocsController::class, 'show'])->name('show');
});
