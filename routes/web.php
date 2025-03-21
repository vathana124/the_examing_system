<?php

use App\Filament\Pages\Auth\OTP as AuthOTP;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Middleware\Auth\AuthMiddleWare;
use App\Http\Middleware\Auth\OtpMiddleWare;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // redirect to login page
    return redirect('/admin');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => AuthMiddleWare::class], function () {

    // otp route
    Route::get('otp', AuthOTP::class)->name('auth.otp')->middleware(OtpMiddleWare::class);
});

// override route logout 
Route::post('/logout', [AuthenticateController::class, 'logout'])->name('filament.admin.auth.logout');