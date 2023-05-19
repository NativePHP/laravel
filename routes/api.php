<?php

use Illuminate\Support\Facades\Route;
use Native\Laravel\Http\Controllers\CreateSecurityCookieController;
use Native\Laravel\Http\Controllers\DispatchEventFromAppController;
use Native\Laravel\Http\Controllers\NativeAppBootedController;
use Native\Laravel\Http\Middleware\PreventRegularBrowserAccess;

Route::group(['middleware' => PreventRegularBrowserAccess::class], function () {
    Route::post('_native/api/booted', NativeAppBootedController::class);
    Route::post('_native/api/events', DispatchEventFromAppController::class);
})->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::get('_native/api/cookie', CreateSecurityCookieController::class);
