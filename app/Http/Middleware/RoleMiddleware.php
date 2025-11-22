<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil role_id user
        $roleId = $user->role_id;

        // Jika termasuk role yang diizinkan
        if (in_array($roleId, $roles)) {
            return $next($request);
        }

        return redirect()->back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
    }
}
