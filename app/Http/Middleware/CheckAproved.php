<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckAproved
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Middleware CheckApproved está siendo ejecutado');

        // Verificamos si el usuario está autenticado y no está aprobado
        if (Auth::check() && !Auth::user()->approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Tu cuenta aún no ha sido aprobada por un administrador. Por favor, espera a que te aprueben.');
        }

        return $next($request);
    }
}




