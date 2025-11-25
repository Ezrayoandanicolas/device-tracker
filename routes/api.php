<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceFingerprintController;

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


Route::post('/fingerprint/store', [DeviceFingerprintController::class, 'generateOrUpdateFingerprint'])->middleware('cors.fingerprint');

Route::get('/fingerprint/by-ip', [DeviceFingerprintController::class, 'getByIp']);

Route::get('/fingerprint/ip-groups', [DeviceFingerprintController::class, 'getAllIpGroups']);

Route::get('/fingerprint/device-ip-map', [DeviceFingerprintController::class, 'getDevicesWithIps']);

Route::get('/fingerprint/by-ip/devices', [DeviceFingerprintController::class, 'getDevicesBySpecificIp']);


