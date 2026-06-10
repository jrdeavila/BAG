<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea el uso de la plataforma a funcionarios cuyo area no este habilitada
 * (salvo funcionarios especiales o superadmin), y a usuarios bloqueados.
 * Complementa la verificacion del login para expulsar tambien sesiones ya abiertas.
 */
class EnsurePlatformAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->hasPlatformAccess()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'usuario' => 'Tu area no esta habilitada para usar la plataforma. Contacta al administrador.',
            ]);
        }

        return $next($request);
    }
}
