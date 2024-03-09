<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function() {
	Route::apiResource('calendarSetting', CalendarSettingController::class);
	Route::apiResource('googleCalendar', GoogleCalendarController::class);
	// Route::apiResource('googleCalendarList', GoogleCalendarListController::class);
	Route::apiResource('googleOauth', GoogleOauthController::class);
	Route::apiResource('user', UserController::class);

	Route::post('googleCalendarList', [\App\Http\Controllers\Api\V1\GoogleCalendarListController::class, 'index']);
	Route::get('event', [\App\Http\Controllers\Api\V1\EventController::class, 'getEvent']);
});
