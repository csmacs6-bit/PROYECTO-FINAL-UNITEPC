<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportAccess
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        abort_unless($token, 401, 'Inicia sesión para continuar.');
        $user = DB::table('access_tokens')->join('usuarios', 'usuarios.id', '=', 'access_tokens.user_id')
            ->where('token', hash('sha256', $token))->where('expires_at', '>', now())
            ->where('usuarios.status', 'Activo')->select('usuarios.*')->first();
        abort_unless($user, 401, 'La sesión venció. Inicia sesión nuevamente.');
        abort_if($user->role === 'Chofer', 403, 'No tienes permiso para administrar registros.');
        $request->attributes->set('transportUser', $user);
        return $next($request);
    }
}
