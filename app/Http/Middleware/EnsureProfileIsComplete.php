<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $exemptRoutes = [
        'doctor.profile.show',  // Asumiendo que crearás esta ruta
        'doctor.profile.update',   // La ruta que guarda el perfil
        'patient.profile.show',
        'patient.profile.update',
    ];
    
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si el usuario no está logueado o la ruta es una de las exentas, no hacemos nada.
        if (!$user || in_array($request->route()->getName(), $this->exemptRoutes)) {
            return $next($request);
        }

        // Si el usuario es un 'admin', no necesita perfil de doctor/paciente. Lo dejamos pasar.
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Si el usuario NO tiene un perfil asociado (es doctor o paciente)...
        if (!$user->profileable) {
            $routeName = '';
            if ($user->hasRole('doctor')) {
                $routeName = 'doctor.profile.show';
            } elseif ($user->hasRole('patient')) {
                $routeName = 'patient.profile.show';
            }

            // Si se definió una ruta para completar el perfil, lo redirigimos.
            if ($routeName) {
                return redirect()->route($routeName)->with('info', 'Por favor, completa la información de tu perfil para continuar.');
            }
        }

        return $next($request);
    }
}
