<?php
// app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Vérifier si le header Authorization existe
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token manquant. Veuillez vous connecter.'
            ], 401);
        }

        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide ou expiré. Veuillez vous reconnecter.'
            ], 401);
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur a la permission
        if (!$user->hasPermission($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas les droits pour accéder à cette page.'
            ], 403);
        }

        return $next($request);
    }
}