<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
    Route::get('/admin/listings', [DashboardController::class, 'listings'])->name('admin.listings');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

Route::middleware('guest')->group(function () {

    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

});