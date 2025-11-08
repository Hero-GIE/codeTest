<?php

use App\Http\Controllers\AdventureController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes (Dashboard, Editing)
Route::middleware(['firebase.auth'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/settings', [DashboardController::class, 'updateWebsiteSettings'])->name('dashboard.settings.update');
    Route::post('/dashboard/upload-image', [DashboardController::class, 'uploadImage'])->name('dashboard.upload.image');
    Route::post('/dashboard/pages/{page}/publish', [DashboardController::class, 'togglePublish'])->name('dashboard.pages.publish');

    // ✅ Add your new profile update route here
    Route::post('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');

    // Analytics routes
    Route::get('/dashboard/analytics', [DashboardController::class, 'getAnalytics'])->name('dashboard.analytics');
    Route::get('/dashboard/analytics/export', [DashboardController::class, 'exportAnalytics'])->name('dashboard.analytics.export');

    // Editor routes
    Route::get('/website/editor/{page?}', [DashboardController::class, 'editor'])->name('website.editor');
    Route::post('/website/pages/{page}', [DashboardController::class, 'updatePageContent'])->name('website.pages.update');

    // Gallery management routes
    Route::prefix('gallery')->group(function () {
        Route::post('/upload', [GalleryController::class, 'uploadImage'])->name('gallery.upload');
        Route::delete('/image/{publitioId}', [GalleryController::class, 'deleteImage'])->name('gallery.delete');
        Route::put('/image/{imageId}', [GalleryController::class, 'updateImage'])->name('gallery.update');
        Route::get('/images', [GalleryController::class, 'getImages'])->name('gallery.images');
    });

    // ✅ Adventure routes (only for authenticated users)
    Route::post('/adventures/create', [AdventureController::class, 'create']);

});

// Public website routes - SPECIFIC ROUTES FIRST
Route::get('/', [WebsiteController::class, 'showPage'])->defaults('page', 'home')->name('website.home');
Route::get('/about', [WebsiteController::class, 'showPage'])->defaults('page', 'about')->name('website.about');
Route::get('/contact', [WebsiteController::class, 'showPage'])->defaults('page', 'contact')->name('website.contact');
Route::get('/gallery', [GalleryController::class, 'show'])->name('gallery');
Route::get('/{page}', [WebsiteController::class, 'showPage'])->name('website.page');
