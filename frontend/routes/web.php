<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'app')->name('login');
Route::view('/register', 'app')->name('register');
Route::view('/dashboard', 'app')->name('dashboard');
Route::view('/chofer/dashboard', 'app')->name('chofer.dashboard');
Route::view('/viajes', 'app')->name('viajes');
Route::view('/camiones', 'app')->name('camiones');
Route::view('/conductores', 'app')->name('conductores');
Route::view('/clientes', 'app')->name('clientes');
Route::view('/cargas', 'app')->name('cargas');
Route::view('/combustible', 'app')->name('combustible');
Route::view('/mantenimiento', 'app')->name('mantenimiento');
Route::view('/gps', 'app')->name('gps');
Route::view('/reportes', 'app')->name('reportes');
Route::view('/usuarios', 'app')->name('usuarios');

Route::redirect('/', '/login');

Route::view('/{any}', 'app')->where('any', '.*');
