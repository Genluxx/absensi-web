<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminHr
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin_hr', 'super_admin'])) {
            abort(403, 'Halaman ini khusus Admin HR / Super Admin.');
        }

        return $next($request);
    }
}