<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'app')->name('login');
Route::view('/register', 'app')->name('register');
Route::view('/dashboard', 'app')->name('dashboard');

Route::redirect('/', '/login');

Route::view('/{any}', 'app')->where('any', '.*');
