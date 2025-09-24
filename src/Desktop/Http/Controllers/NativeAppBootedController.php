<?php

namespace Native\Desktop\Http\Controllers;

use Illuminate\Http\Request;
use Native\Desktop\Events\App\ApplicationBooted;

class NativeAppBootedController
{
    public function __invoke(Request $request)
    {
        $provider = app(config('nativephp.provider'));
        $provider->boot();

        event(new ApplicationBooted);

        return response()->json([
            'success' => true,
        ]);
    }
}
