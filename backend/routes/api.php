<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Middleware\TransportAccess;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegistrationController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [RegistrationController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [RegistrationController::class, 'logout'])->middleware('throttle:30,1');
Route::middleware([TransportAccess::class, 'throttle:120,1'])->group(function () {
    Route::get('/{module}', [RegistrationController::class, 'index']);
    Route::post('/{module}', [RegistrationController::class, 'store']);
});
