<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\Photographer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Owner\NotificationController as OwnerNotificationController;


Route::redirect('/', '/login');

    Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::controller(SocialiteController::class)->group(function () {
    Route::get('/auth/google','redirectToGoogle') ->name('auth.google');
    Route::get('/auth/google-callback',  'handleGoogleCallback')->name('auth.google.callback');
});

    Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/theme/{mode}', function ($mode) {
    Session::put('theme', $mode);
    return back();
    })->name('theme.set');


    Route::get('/language/{lang}', function ($lang) {
        Session::put('language', $lang);
        return back();
    })->name('lang.set');

        // Admin 
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users',                [Admin\UserController::class, 'index'])->name('users');
        Route::get('/users/{user}',         [Admin\UserController::class, 'show'])->name('users.detail');
        Route::delete('/users/{user}',      [Admin\UserController::class, 'destroy'])->name('users.delete');
        Route::post('/users/{user}/toggle', [Admin\UserController::class, 'toggleStatus'])->name('users.toggle');

        // Listings
        Route::get('/listings',                    [Admin\ListingController::class, 'index'])->name('listings');
        Route::get('/listings/{listing}',          [Admin\ListingController::class, 'show'])->name('listings.show');
        Route::post('/listings/{listing}/toggle',  [Admin\ListingController::class, 'toggleApproval'])->name('listings.toggle');
        Route::post('/listings/{listing}/approve', [Admin\ListingController::class, 'approve'])->name('listings.approve');
        Route::post('/listings/{listing}/reject',  [Admin\ListingController::class, 'reject'])->name('listings.reject');
        Route::delete('/listings/{listing}',       [Admin\ListingController::class, 'destroy'])->name('listings.delete');

        // Profile
        Route::get('/profile',           [Admin\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile',          [Admin\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',    [Admin\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/profile/password', [Admin\ProfileController::class, 'updatePassword'])->name('profile.password');

        //nav
        Route::get('/notifications',           [NotificationController::class, 'index'])   ->name('notifications');
        Route::get('/notifications/unread',    [NotificationController::class, 'unread'])  ->name('notifications.unread');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        
    });

        // Owner 
        Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [Owner\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/listings',  [Owner\ListingController::class, 'index'])->name('listings');

        // Bookings
        Route::get('/bookings',                  [Owner\BookingController::class, 'index'])->name('bookings');
        Route::post('/bookings/{booking}/accept', [Owner\BookingController::class, 'accept'])->name('bookings.accept');
        Route::post('/bookings/{booking}/reject', [Owner\BookingController::class, 'reject'])->name('bookings.reject');

        // Locations (the listings owners create)
        Route::get('/locations/create',         [Owner\LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations',               [Owner\LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{location}/edit', [Owner\LocationController::class, 'edit'])->name('locations.edit');
        Route::put('/locations/{location}',      [Owner\LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}',   [Owner\LocationController::class, 'destroy'])->name('locations.destroy');

        //Notifications
        Route::get('/notifications',           [OwnerNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/unread',    [OwnerNotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('/notifications/read-all', [OwnerNotificationController::class, 'readAll'])->name('notifications.read-all');

        // Profile
        Route::get('/profile',                [Owner\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile/update',        [Owner\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',         [Owner\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        //Route::post('/profile/payment',       [Owner\ProfileController::class, 'updatePayment'])->name('profile.payment');
        Route::post('/profile/password',      [Owner\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

        // Photographer 
        Route::middleware('role:photographer')->prefix('photographer')->name('photographer.')->group(function () {
        Route::get('/dashboard', [Photographer\DashboardController::class, 'index'])->name('dashboard');

        // Browse approved listings
        Route::get('/listings',                  [Photographer\BrowseController::class, 'index'])->name('listings');
        Route::get('/listings/{location}',       [Photographer\BrowseController::class, 'show'])->name('listings.show');
        Route::get('/listings/{location}/book',  [Photographer\BookingController::class, 'create'])->name('listings.book');
        Route::post('/listings/{location}/book', [Photographer\BookingController::class, 'store'])->name('listings.book.store');

        // My bookings
        Route::get('/bookings',                   [Photographer\BookingController::class, 'index'])->name('bookings');
        Route::post('/bookings/{booking}/cancel', [Photographer\BookingController::class, 'cancel'])->name('bookings.cancel');

        // Profile
        Route::get('/profile',           [Photographer\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile',          [Photographer\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',    [Photographer\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/profile/password', [Photographer\ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
