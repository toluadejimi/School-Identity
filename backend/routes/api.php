<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusFareController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\IdentityController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\StudentRegistrationController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Middleware\EnsureDeviceHeader;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json([
        'status' => 'ok',
        'service' => 'School Identity Passa API',
    ]));

    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', EnsureDeviceHeader::class])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/devices/register', [DeviceController::class, 'register']);

        Route::post('/identity/scan', [IdentityController::class, 'scan']);
        Route::post('/students/register', [StudentRegistrationController::class, 'store']);

        Route::post('/clinic/check-in', [ClinicController::class, 'checkIn']);
        Route::patch('/clinic/visits/{visit}', [ClinicController::class, 'update']);

        Route::post('/library/check-in', [LibraryController::class, 'checkIn']);
        Route::post('/library/check-out', [LibraryController::class, 'checkOut']);
        Route::get('/library/history', [LibraryController::class, 'history']);

        Route::get('/attendance/sessions', [AttendanceController::class, 'sessions']);
        Route::post('/attendance/scan', [AttendanceController::class, 'scan']);

        Route::get('/exams', [ExamController::class, 'index']);
        Route::post('/exams/scan', [ExamController::class, 'scan']);

        Route::get('/bus-routes', [BusFareController::class, 'routes']);
        Route::post('/bus-fare/scan', [BusFareController::class, 'pay']);

        Route::get('/wallet/{student}', [WalletController::class, 'show']);
    });
});
