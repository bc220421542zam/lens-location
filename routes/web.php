<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Default route 
Route::get('/', function () {
    return redirect('/login');
});

// Home & Logout 
Route::middleware('auth')->group(function(){
    
    Route::get('/home', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// Login & Register
Route::middleware('guest')->group(function(){

    Route::view('/register','auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::view('/login','auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});