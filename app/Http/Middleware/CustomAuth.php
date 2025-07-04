<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Aquí puedes agregar la lógica de autenticación personalizada
        // Por ejemplo, verificar un token en los encabezados
        $token = $request->header('Authorization');

        if ($token && $this->isValidToken($token)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Verificar si el token es válido.
     *
     * @param  string  $token
     * @return bool
     */
    private function isValidToken($token)
    {
        $validToken = env('CUSTOM_AUTH_TOKEN');  // Lee la clave desde el .env
        return $token === $validToken;
    }

}
