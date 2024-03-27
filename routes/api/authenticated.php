<?php

use App\Http\Controllers\ContactUsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ScanController;


/*
|--------------------------------------------------------------------------
|  Authenticated Public API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('doctors', DoctorController::class)->only(['show', 'index']);
    Route::apiResource('scans', ScanController::class)->only('store');
    Route::apiResource('contactus', ContactUsController::class)->only('store');
});
