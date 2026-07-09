<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Customer\FavoriteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Owner\NotificationController as OwnerNotificationController;


Route::redirect('/', '/login');

    Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Password Reset Routes
    Route::view('/forgot-password', 'auth.forgot-password')->name('forgot.password');
    Route::view('/reset-password', 'auth.reset-password')->name('reset.password');
 
});
Route::controller(SocialiteController::class)->group(function () {
    Route::get('/auth/google','redirectToGoogle') ->name('auth.google');
    Route::get('/auth/google-callback',  'handleGoogleCallback')->name('auth.google.callback');
});

    Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

        // Notifications
        Route::get('/notifications',           [NotificationController::class, 'index'])   ->name('notifications');
        Route::get('/notifications/unread',    [NotificationController::class, 'unread'])  ->name('notifications.unread');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])
        ->name('admin.profile.notifications');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');
        
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
        Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])
        ->name('owner.profile.notifications');

        // Profile
        Route::get('/profile',                [Owner\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile/update',        [Owner\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',         [Owner\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        //Route::post('/profile/payment',       [Owner\ProfileController::class, 'updatePayment'])->name('profile.payment');
        Route::post('/profile/password',      [Owner\ProfileController::class, 'updatePassword'])->name('profile.password');

    });

        // Customer 
        Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [Customer\DashboardController::class, 'index'])->name('dashboard');

        // Browse approved listings
        Route::get('/listings',                  [Customer\BrowseController::class, 'index'])->name('listings');
        Route::get('/listings/{location}',       [Customer\BrowseController::class, 'show'])->name('listings.show');
        Route::get('/listings/{location}/book',  [Customer\BookingController::class, 'create'])->name('listings.book');
        Route::post('/listings/{location}/book', [Customer\BookingController::class, 'store'])->name('listings.book.store');

        // My bookings
        Route::get('/bookings',                   [Customer\BookingController::class, 'index'])->name('bookings');
        Route::post('/bookings/{booking}/cancel', [Customer\BookingController::class, 'cancel'])->name('bookings.cancel');

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
        Route::post('/favorites/{location}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

        // Profile
        Route::get('/profile',           [Customer\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile',          [Customer\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',    [Customer\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/profile/password', [Customer\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])
        ->name('customer.profile.notifications');

    });

    Route::get('/test-mail', function () {
    Mail::raw('Hello, this is a quick test email from Laravel!', function ($message) {
        $message->to('your-test-email@example.com')
            ->subject('Laravel Live Mail Test');
    });
 
    return 'Test email sent!';
    });

});
