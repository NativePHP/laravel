<?php

namespace Native\Laravel\Http\Controllers;

use Illuminate\Http\Request;

class DispatchEventFromAppController
{
    public function __invoke(Request $request)
    {
        $event = $request->get('event');
        if (class_exists($event)) {
            $event = new $event(...$request->get('payload', []));

            event($event);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
