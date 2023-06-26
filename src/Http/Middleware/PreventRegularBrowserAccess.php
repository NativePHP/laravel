<?php

namespace Native\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRegularBrowserAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Explicitly skip for the cookie-setting route
        if ($request->path() === '_native/api/cookie') {
            return $next($request);
        }

        $cookie = $request->cookie('_php_native');
        $header = $request->header('X-NativePHP-Secret');

        if ($cookie && $cookie === config('nativephp-internal.secret')) {
            return $next($request);
        }

        if ($header && $header === config('nativephp-internal.secret')) {
            return $next($request);
        }

        return abort(403);
    }
}
