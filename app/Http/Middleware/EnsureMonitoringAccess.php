<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMonitoringAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $expiresAt = (int) $request->session()->get('monitoring_access_until', 0);

        abort_if($expiresAt <= now()->timestamp, 403, 'Akses monitoring tidak tersedia atau telah berakhir.');

        return $next($request);
    }
}
