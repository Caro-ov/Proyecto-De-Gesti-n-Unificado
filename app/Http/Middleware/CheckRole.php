<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (auth()->user()->role !== $role) {
            abort(403, 'No tienes permiso para acceder aquí.');
        }

        return $next($request);
    }
}
