<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    // Uso: 'rol:admin' o 'rol:admin,empleado'
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $actual = $request->user()?->role?->nombre;

        if (! $actual || ! in_array($actual, $roles, true)) {
            abort(403, 'No tienes permiso para entrar aquí.');
        }

        return $next($request);
    }
}
