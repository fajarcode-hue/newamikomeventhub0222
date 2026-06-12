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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login dan memiliki role 'admin' (sesuai data seeder)
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // 2. Jika bukan admin atau belum login, tendang kembali ke halaman login dengan pesan error
        return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki hak akses ke halaman administrator.');
    }
}