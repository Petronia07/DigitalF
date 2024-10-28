<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Vérifier si l'utilisateur est authentifié et si son rôle est "admin"
         if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Permettre l'accès si l'utilisateur est un admin
        }

        // Rediriger ou renvoyer un message d'erreur si l'utilisateur n'est pas un admin
        return response()->json(['message' => 'Accès refusé : vous n\'avez pas les permissions nécessaires'], 403);
       
    }
}
