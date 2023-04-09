<?php

namespace Native\Laravel\Http\Controllers;

use Illuminate\Http\Request;

class NativeAppBootedController
{
    public function __invoke(Request $request)
    {
        $provider = app(config('native-php.provider'));
        $provider->boot();

        return response()->json([
            'success' => true,
        ]);
    }
}
