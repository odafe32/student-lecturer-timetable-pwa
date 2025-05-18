<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has the required role
        switch ($role) {
            case 'admin':
                if (!$user->isAdmin()) {
                    abort(403, 'Unauthorized action.');
                }
                break;
            case 'lecturer':
                if (!$user->isLecturer()) {
                    abort(403, 'Unauthorized action.');
                }
                break;
            case 'student':
                if (!$user->isStudent()) {
                    abort(403, 'Unauthorized action.');
                }
                break;
            default:
                abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}