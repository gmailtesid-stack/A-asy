<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetDeviceLocalization
{
    public function handle(Request $request, Closure $next): Response
    {
        $deviceTimezone = $request->header('X-Device-Timezone');
        $lat = $request->header('X-Device-Lat');
        $lng = $request->header('X-Device-Lng');

        // Logic for GPS to Timezone (Simplification for ERP Core)
        if (!$deviceTimezone && $lat && $lng) {
            $deviceTimezone = $this->resolveTimezoneFromGps($lat, $lng);
        }
        
        if ($deviceTimezone && in_array($deviceTimezone, timezone_identifiers_list())) {
            config(['app.timezone' => $deviceTimezone]);
            date_default_timezone_set($deviceTimezone);
            
            // Persist to user for future session consistency
            if (Auth::check() && Auth::user()->timezone !== $deviceTimezone) {
                Auth::user()->update(['timezone' => $deviceTimezone]);
            }
        } elseif (Auth::check() && Auth::user()->timezone) {
            config(['app.timezone' => Auth::user()->timezone]);
            date_default_timezone_set(Auth::user()->timezone);
        }

        // 2. Localization Handling (Language)
        $locale = $request->header('Accept-Language', 'id');
        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
        }
        
        App::setLocale(substr($locale, 0, 2));

        return $next($request);
    }

    /**
     * Resolve timezone from GPS coordinates.
     * In production, this would use a library or cached API call.
     */
    private function resolveTimezoneFromGps($lat, $lng): ?string
    {
        // Mock logic for common regions in our tests/prod
        // Jakarta approx: -6, 106
        if ($lat < 0 && $lng > 100 && $lng < 110) return 'Asia/Jakarta';
        // Tokyo approx: 35, 139
        if ($lat > 30 && $lng > 130 && $lng < 150) return 'Asia/Tokyo';
        // Singapore approx: 1, 103
        if ($lat > 1 && $lat < 2 && $lng > 103 && $lng < 104) return 'Asia/Singapore';

        return null;
    }
}
