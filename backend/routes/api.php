<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/health-atm/measurements', [\App\Http\Controllers\Api\HealthAtmMeasurementController::class, 'store']);

Route::get('/measurements', [\App\Http\Controllers\Api\MeasurementController::class, 'index']);
Route::get('/measurements/latest', [\App\Http\Controllers\Api\MeasurementController::class, 'latest']);
Route::get('/measurements/{measurement}', [\App\Http\Controllers\Api\MeasurementController::class, 'show']);

Route::get('/whiteList/channel/common/logoConfig', [\App\Http\Controllers\Api\LogoConfigController::class, 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
