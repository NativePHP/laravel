<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Native\Laravel\Http\Controllers\NativeAppBootedController;
use Native\Laravel\Http\Middleware\PreventRegularBrowserAccess;
use Native\Laravel\Http\Controllers\CreateSecurityCookieController;
use Native\Laravel\Http\Controllers\DispatchEventFromAppController;

Route::group(['middleware' => PreventRegularBrowserAccess::class], function () {
    Route::post('_native/api/booted', NativeAppBootedController::class);
    Route::post('_native/api/events', DispatchEventFromAppController::class);
})->withoutMiddleware(VerifyCsrfToken::class);

Route::get('_native/api/cookie', CreateSecurityCookieController::class);
