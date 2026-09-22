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
