<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginForSiswa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Periksa apakah pengguna memiliki role 'siswa'
        if (Auth::check() && Auth::user()->role !== 'siswa') {
            // Jika bukan siswa, logout dan arahkan ke halaman login dengan pesan error
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Hanya siswa yang dapat login.']);
        }

        return $next($request);
    }
}