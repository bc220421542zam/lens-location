<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Customer\FavoriteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Owner\NotificationController as OwnerNotificationController;
use App\Http\Controllers\Customer\NotificationController as CustomerNotificationController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Owner\ReviewController as OwnerReviewController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Owner\EarningController;
use App\Http\Controllers\Owner\StripeConnectController;
use App\Http\Controllers\StripeWebhookController;



Route::redirect('/', '/register');

Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset Routes
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::controller(SocialiteController::class)->group(function () {
    Route::get('/auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('/auth/google-callback', 'handleGoogleCallback')->name('auth.google.callback');
});

// Stripe webhook — must sit outside auth/role middleware since Stripe's servers
// hit this directly and aren't logged in as any of your users. Safe only
// because the handler verifies the signature before reading the payload.
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==================== ADMIN ====================
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
        Route::post('/listings/{listing}/approve', [Admin\ListingController::class, 'approve'])->name('listings.approve');
        Route::post('/listings/{listing}/reject',  [Admin\ListingController::class, 'reject'])->name('listings.reject');
        Route::delete('/listings/{listing}',       [Admin\ListingController::class, 'destroy'])->name('listings.delete');

        // Profile
        Route::get('/profile',           [Admin\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile',          [Admin\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',    [Admin\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/profile/password', [Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/notifications', [Admin\ProfileController::class, 'updateNotifications'])->name('profile.notifications');

        // Notifications
        Route::get('/notifications',           [NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/unread',    [NotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');

        // Categories
        Route::get('/categories',               [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories',              [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',    [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');

        // Reviews
        Route::get('/reviews',                  [AdminReviewController::class, 'index'])->name('reviews');
        Route::post('/reviews/{review}/toggle', [AdminReviewController::class, 'toggleVisibility'])->name('reviews.toggle');
        Route::delete('/reviews/{review}',      [AdminReviewController::class, 'destroy'])->name('reviews.delete');

        // Payments
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments');
    });

    // ==================== OWNER ====================
    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [Owner\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/listings',  [Owner\ListingController::class, 'index'])->name('listings');

        // Bookings
        Route::get('/bookings',                   [Owner\BookingController::class, 'index'])->name('bookings');
        Route::post('/bookings/{booking}/accept', [Owner\BookingController::class, 'accept'])->name('bookings.accept');
        Route::post('/bookings/{booking}/reject', [Owner\BookingController::class, 'reject'])->name('bookings.reject');

        // Locations 
        Route::get('/locations/create',          [Owner\LocationController::class, 'create'])->name('locations.create');
        Route::get('/locations/{location}',       [Owner\LocationController::class, 'show'])->name('locations.show');
        Route::post('/locations',                [Owner\LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{location}/edit',  [Owner\LocationController::class, 'edit'])->name('locations.edit');
        Route::put('/locations/{location}',       [Owner\LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}',    [Owner\LocationController::class, 'destroy'])->name('locations.destroy');

        // Notifications
        Route::get('/notifications',           [OwnerNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/unread',    [OwnerNotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('/notifications/read-all', [OwnerNotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::post('/notifications/{id}/read', [OwnerNotificationController::class, 'read'])->name('notifications.read');

        //Messages
        Route::get('/messages/{conversation?}', [MessageController::class, 'index'])->name('messages');
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

        // Reviews
        Route::get('/reviews', [OwnerReviewController::class, 'index'])->name('reviews');

        // Earnings
        Route::get('/earnings', [EarningController::class, 'index'])->name('earnings');

        // Stripe Connect payouts
        Route::post('/stripe/connect',   [StripeConnectController::class, 'onboard'])->name('stripe.connect');
        Route::get('/stripe/return',     [StripeConnectController::class, 'callbackReturn'])->name('stripe.return');
        Route::post('/stripe/refresh',   [StripeConnectController::class, 'refresh'])->name('stripe.refresh');
        Route::post('/stripe/dashboard', [StripeConnectController::class, 'dashboard'])->name('stripe.dashboard');

        // Profile
        Route::get('/profile',                [Owner\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile/update',        [Owner\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',         [Owner\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/profile/password',      [Owner\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/notifications', [Owner\ProfileController::class, 'updateNotifications'])->name('profile.notifications');

    });

    // ==================== CUSTOMER ====================
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
        Route::get('/favorites',                    [FavoriteController::class, 'index'])->name('favorites');
        Route::post('/favorites/{location}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

        // Reviews
        Route::get('/bookings/{booking}/review', [CustomerReviewController::class, 'create'])->name('bookings.review');
        Route::post('/bookings/{booking}/review', [CustomerReviewController::class, 'store'])->name('bookings.review.store');

        // Messages
        Route::get('/messages/{conversation?}', [MessageController::class, 'index'])->name('messages');
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/listings/{location}/message', [MessageController::class, 'startWithOwner'])->name('listings.message');

        // Notifications
        Route::get('/notifications',            [CustomerNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/unread',     [CustomerNotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('/notifications/read-all',  [CustomerNotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::post('/notifications/{id}/read', [CustomerNotificationController::class, 'read'])->name('notifications.read');

        // Profile
        Route::get('/profile',                [Customer\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile',               [Customer\ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo',         [Customer\ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::post('/profile/password',      [Customer\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/notifications', [Customer\ProfileController::class, 'updateNotifications'])->name('profile.notifications');

        // Payments (Stripe Checkout). POST because it creates a Stripe session
        // and a transaction row, so it must not be prefetchable.
        Route::post('/bookings/{booking}/pay', [PaymentController::class, 'pay'])->name('bookings.pay');
        Route::get('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments');

    });

});