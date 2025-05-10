<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Attendance\WebhookController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'attendance' , 'middleware' => 'ability:access-api'], function () {
        Route::post('/submit', [AttendanceController::class, 'submit']);
        Route::post('/get-att-gps-by-karyawan-id', [AttendanceController::class, 'getDataByKaryawanId']);
        Route::post('/get-profile', [AuthController::class, 'getProfile']);
    });
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh-token', [AuthController::class, 'refreshToken'])->middleware('ability:issue-access-token');
});

Route::post('/webhook/attendance/scanlog/{idOrganisasi}', [WebhookController::class, 'get_att_tcf']);
