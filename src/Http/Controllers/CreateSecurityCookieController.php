<?php

namespace Native\Laravel\Http\Controllers;

use Illuminate\Http\Request;

class CreateSecurityCookieController
{
    public function __invoke(Request $request)
    {
        abort_if($request->get('secret') !== config('native-php.secret'), 403);

        return redirect('/')->cookie(cookie(
            name: '_php_native',
            value: config('native-php.secret'),
            domain: 'localhost',
            httpOnly: true,
        ));
    }
}
