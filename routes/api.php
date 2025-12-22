<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceFingerprintController;
use App\Http\Controllers\Api\BacklinkFetchController;

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


Route::post('/fetch/backlink/posts', [BacklinkFetchController::class, 'fetch']);
Route::post('/backlink/claim', [BacklinkFetchController::class, 'claim']);
Route::get('/backlink/get', [BacklinkFetchController::class, 'get']);

Route::get('/url-backlinks', [BacklinkFetchController::class, 'index'])->withoutMiddleware(['auth:sanctum', 'auth']);
Route::post('/url-backlinks', [BacklinkFetchController::class, 'store'])->withoutMiddleware(['auth:sanctum', 'auth']);
Route::put('/url-backlinks/{id}', [BacklinkFetchController::class, 'update'])->withoutMiddleware(['auth:sanctum', 'auth']);
Route::get('/url-backlinks/usage-count', [BacklinkFetchController::class, 'usageCount'])->withoutMiddleware(['auth:sanctum', 'auth']);
Route::get('/url-backlinks/{id}/usage-count', [BacklinkFetchController::class, 'usageCountById'])->withoutMiddleware(['auth:sanctum', 'auth']);
