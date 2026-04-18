<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
    Route::get('/admin/listings', [DashboardController::class, 'listings'])->name('admin.listings');
    Route::get('/admin/profile', [DashboardController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/profile', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Owner Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/owner/listings', [OwnerController::class, 'listings'])->name('owner.listings');
    Route::get('/owner/bookings', [OwnerController::class, 'bookings'])->name('owner.bookings');
    Route::get('/owner/profile', [OwnerController::class, 'profile'])->name('owner.profile');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});