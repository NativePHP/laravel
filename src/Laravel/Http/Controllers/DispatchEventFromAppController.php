<?php

namespace Native\Laravel\Http\Controllers;

use Illuminate\Http\Request;

class DispatchEventFromAppController
{
    public function __invoke(Request $request)
    {
        $event = $request->get('event');
        $payload = $request->get('payload', []);

        if (class_exists($event)) {
            $event = new $event(...$payload);
            event($event);
        } else {
            event($event, $payload);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
