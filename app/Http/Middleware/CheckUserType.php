<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $type (admin atau user)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Jika tipe adalah admin, hanya administrasi yang bisa akses
        if ($type === 'admin' && $user->role !== 'administrasi') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Jika tipe adalah user, hanya guru dan murid yang bisa akses
        if ($type === 'user' && $user->role === 'administrasi') {
            abort(403, 'Halaman ini hanya untuk guru dan murid.');
        }

        return $next($request);
    }
}
