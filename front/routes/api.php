<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', function (Request $request) {
    return response()->json([
        'message' => 'Registro visual recibido. Endpoint stub sin logica real.',
        'data' => $request->only([
            'fullName',
            'username',
            'driver',
        ]),
    ]);
});

Route::post('/login', function (Request $request) {
    return response()->json([
        'message' => 'Login visual recibido. Endpoint stub sin autenticacion real.',
        'data' => $request->only([
            'username',
        ]),
    ]);
});

Route::prefix('mock')->group(function () {
    Route::get('/dashboard', fn () => response()->json(['message' => 'Dashboard mock']));
    Route::get('/viajes', fn () => response()->json(['message' => 'Viajes mock']));
    Route::get('/camiones', fn () => response()->json(['message' => 'Camiones mock']));
    Route::get('/conductores', fn () => response()->json(['message' => 'Conductores mock']));
    Route::get('/clientes', fn () => response()->json(['message' => 'Clientes mock']));
    Route::get('/cargas', fn () => response()->json(['message' => 'Cargas mock']));
    Route::get('/combustible', fn () => response()->json(['message' => 'Combustible mock']));
    Route::get('/mantenimiento', fn () => response()->json(['message' => 'Mantenimiento mock']));
    Route::get('/gps', fn () => response()->json(['message' => 'GPS mock']));
    Route::get('/reportes', fn () => response()->json(['message' => 'Reportes mock']));
    Route::get('/usuarios', fn () => response()->json(['message' => 'Usuarios mock']));
});
