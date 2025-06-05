<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStillExists
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user login tapi datanya tidak ada (sudah dihapus), logout paksa
        if (Auth::check() && !Auth::user()) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda sudah tidak tersedia.']);
        }
        return $next($request);
    }
} 