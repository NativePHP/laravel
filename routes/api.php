<?php

use Illuminate\Support\Facades\Route;
use Native\Laravel\Http\Controllers\DispatchEventFromAppController;
use Native\Laravel\Http\Controllers\NativeAppBootedController;
use Native\Laravel\Http\Middleware\PreventRegularBrowserAccess;

Route::group(['middleware' => PreventRegularBrowserAccess::class], function () {
    Route::post('_native/api/booted', NativeAppBootedController::class);
    Route::post('_native/api/events', DispatchEventFromAppController::class);
});
