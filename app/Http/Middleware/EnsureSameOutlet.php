<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSameOutlet
{
    public function handle(Request $request, Closure $next): Response
    {
        $user     = $request->user();
        $outletId = $request->route('outlet_id')
            ?? $request->input('outlet_id')
            ?? null;

        // Super Admin bypass semua cek
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        if ($outletId && $user && (int) $outletId !== (int) $user->outlet_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akses outlet lain ditolak.'], 403);
            }
            abort(403, 'Anda tidak diizinkan mengakses data outlet lain.');
        }

        return $next($request);
    }
}
