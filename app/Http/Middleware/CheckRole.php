<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Obtener el usuario autenticado
        $user = $request->user();
        
        if (!$user) {
            abort(403, 'No autenticado');
        }
        // Verificar si el usuario está activo
        if ($user->EstadoUsuario=='B') {
            abort(403, 'Usuario dado de baja');
        }
        // Verificar si el usuario tiene alguno de los roles requeridos
        if (!in_array($user->Rol, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta ruta');
        }

        return $next($request);
    }
}
