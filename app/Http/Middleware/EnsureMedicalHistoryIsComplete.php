<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureMedicalHistoryIsComplete
{
    /**
     * Rutas que se exentarán de esta validación.
     *
     * @var array
     */
    protected $exemptRoutes = [
        'patient.medical-history.index',
        'patient.medical-history.update',

        'patient.profile.show',
        'patient.profile.update',

        'logout', 
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || in_array($request->route()->getName(), $this->exemptRoutes)) {
            return $next($request);
        }

        if (!$user->hasRole('patient')) {
            return $next($request);
        }

        if (!$user->profileable) {
            return $next($request);
        }

        if (!$user->profileable->pathology) {
            return redirect()->route('patient.medical-history.index')
                         ->with('info', 'Por favor, completa tu historial médico para continuar.');
        }

        return $next($request);
    }
}