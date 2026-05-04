<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Anda tidak memiliki izin ('.$permission.') untuk mengakses halaman ini.');
        }

        // FASE 6: Scoped Permission Check (Location Isolation)
        $requestOutletId = $request->input('outlet_id') ?? $request->route('outlet_id');
        if ($requestOutletId && ! $user->isSuperAdmin()) {
            if ((int)$requestOutletId !== (int)$user->outlet_id) {
                // If it's a manager, they might have access to the whole branch
                if (!empty($user->branch_id)) {
                    // Logic to check if outlet belongs to branch can be added here
                    // For now, strict outlet check for safety
                }
                
                abort(403, 'Akses lokasi ditolak. Anda hanya diizinkan di outlet terdaftar.');
            }
        }

        return $next($request);
    }
}
