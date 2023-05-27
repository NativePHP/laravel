<?php

namespace Native\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRegularBrowserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $cookie = $request->cookie('_php_native');
        $header = $request->header('X-NativePHP-Secret');

        if ($cookie && $cookie === config('nativephp.secret')) {
            return $next($request);
        }

        if ($header && $header === config('nativephp.secret')) {
            return $next($request);
        }

        return abort(403);
    }
}
