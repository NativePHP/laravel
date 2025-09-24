<?php

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Native\Desktop\Http\Controllers\CreateSecurityCookieController;
use Native\Desktop\Http\Controllers\DispatchEventFromAppController;
use Native\Desktop\Http\Controllers\NativeAppBootedController;
use Native\Desktop\Http\Middleware\PreventRegularBrowserAccess;

Route::group(['middleware' => PreventRegularBrowserAccess::class], function () {
    Route::post('_native/api/booted', NativeAppBootedController::class);
    Route::post('_native/api/events', DispatchEventFromAppController::class);
})->withoutMiddleware(VerifyCsrfToken::class);

Route::get('_native/api/cookie', CreateSecurityCookieController::class);
