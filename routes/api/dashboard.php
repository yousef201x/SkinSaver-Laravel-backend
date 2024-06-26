<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ScanController;

/*
|--------------------------------------------------------------------------
| Dashboard API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::apiResource('doctors', DoctorController::class)->except(['index', 'show']);
    Route::apiResource('scans', ScanController::class)->except('store');
    Route::apiResource('contactus', ContactUsController::class)->except('store');
});
