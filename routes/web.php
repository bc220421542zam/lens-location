<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PhotographerController;

Route::get('/', function () {
    return redirect('/login');
});

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
    Route::get('/admin/listings', [DashboardController::class, 'listings'])->name('admin.listings');
    Route::get('/admin/profile', [DashboardController::class, 'profile'])->name('admin.profile');
    
    Route::get('/admin/users/{id}', [DashboardController::class, 'userDetail'])->name('admin.users.detail');
    Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
    Route::delete('/admin/users/{id}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/users/{id}/toggle', [DashboardController::class, 'toggleStatus'])->name('admin.users.toggle');

    Route::post('/admin/listings/{id}/toggle', [DashboardController::class, 'toggleListing'])->name('admin.listings.toggle');
    Route::delete('/admin/listings/{id}', [DashboardController::class, 'deleteListing'])->name('admin.listings.delete');
    Route::get('/admin/listings/{id}', [DashboardController::class, 'showListing'])->name('admin.listings.show');

    Route::post('/admin/profile', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Owner Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/owner/listings',         [OwnerController::class, 'listings'])->name('owner.listings');
    Route::get('/owner/bookings',         [OwnerController::class, 'bookings'])->name('owner.bookings');
    Route::get('/owner/profile',          [OwnerController::class, 'profile'])->name('owner.profile');
    
   Route::get('/owner/locations/create',    [LocationController::class, 'create'])->name('owner.locations.create');
    Route::post('/owner/locations',          [LocationController::class, 'store'])->name('owner.locations.store');
    Route::get('/owner/locations/{id}/edit', [LocationController::class, 'edit'])->name('owner.locations.edit');
    Route::put('/owner/locations/{id}',      [LocationController::class, 'update'])->name('owner.locations.update');
    Route::delete('/owner/locations/{id}',   [LocationController::class, 'destroy'])->name('owner.locations.destroy');
});

// photographer Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/photographer/dashboard', [PhotographerController::class, 'dashboard'])->name('photographer.dashboard');
    Route::get('/photographer/bookings', [PhotographerController::class, 'bookings'])->name('photographer.bookings');
    Route::get('/photographer/listings', [PhotographerController::class, 'listings'])->name('photographer.listings');
    Route::get('/photographer/profile', [PhotographerController::class, 'profile'])->name('photographer.profile');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});