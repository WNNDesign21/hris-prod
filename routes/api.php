<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Attendance\WebhookController;
use App\Http\Controllers\Api\APILembure\LembureController;


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'attendance', 'middleware' => 'ability:access-api'], function () {
        Route::post('/submit', [AttendanceController::class, 'submit']);
        Route::post('/get-att-gps-by-karyawan-id', [AttendanceController::class, 'getDataByKaryawanId']);
        Route::post('/get-profile', [AuthController::class, 'getProfile']);
    });
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh-token', [AuthController::class, 'refreshToken'])->middleware('ability:issue-access-token');
});
Route::post('/webhook/attendance/scanlog/{idOrganisasi}', [WebhookController::class, 'get_att_tcf']);

Route::group(['middleware' => ['auth']], function () {

});

//Lembur-E
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::get('/lembure/pengajuan-lembur/get-data-lembur/{idlembur}', [LembureController::class, 'get_data_lembur']);
    Route::get('/lembure/pengajuan-lembur/get-data-karyawan-lembur', [LembureController::class, 'getDataKaryawanLembur']);
    Route::post('/lembure/review-lembur/get-review-lembur-detail', [LembureController::class, 'get_review_lembur_detail']);
    Route::get('/lembure/pengajuan-lembur/get-attachment-lembur/{idLembur}', [LembureController::class, 'get_attachment_lembur']);
    Route::post('/export-slip-lembur', [LembureController::class, 'export_slip_lembur'])->name('home.export-slip-lembur');

    Route::middleware(['role:atasan|personalia|personalia-lembur,sanctum'])->group(function () {
        // Approval Lembur API
        Route::post('/approval-lembur-datatable', [LembureController::class, 'approval_lembur_datatable']);
        Route::patch('/approval-lembur/rejected/{idLembur}', [LembureController::class, 'rejected'])->name('lembure.approval-lembur.rejected');
        Route::patch('/approval-lembur/checked/{idLembur}', [LembureController::class, 'checked'])->name('lembure.approval-lembur.checked');
        Route::patch('/approval-lembur/approved/{idLembur}', [LembureController::class, 'approved'])->name('lembure.approval-lembur.approved');
        Route::patch('/approval-lembur/legalized/{idLembur}', [LembureController::class, 'legalized'])->name('lembure.approval-lembur.legalized');
        Route::patch('/approval-lembur/rollback/{idLembur}', [LembureController::class, 'rollback'])->name('lembure.approval-lembur.rollback');

        // Review Lembur API
        Route::post('/review-lembur-datatable', [LembureController::class, 'review_lembur_datatable']);
        Route::patch('/review-lembur/reviewed', [LembureController::class, 'reviewed'])->name('lembure.review-lembur.reviewed');
        Route::patch('/review-lembur/rejected/{idDetailLembur}', [LembureController::class, 'review_lembur_rejected'])->name('lembure.review-lembur.rejected');
    });

    Route::middleware(['role:atasan|member|admin-dept,sanctum'])->group(function () {
        // Pengajuan Lembur API
        Route::get('/access/lembur-e/pengajuan-lembur', [LembureController::class, 'pengajuanLemburAccess']);
        Route::post('/pengajuan-lembur-datatable', [LembureController::class, 'pengajuanLemburDatatable']);
        Route::post('/pengajuan-lembur/store', [LembureController::class, 'store']);
        Route::patch('/pengajuan-lembur/done/{idLembur}', [LembureController::class, 'done'])->name('lembure.pengajuan-lembur.done');
        Route::post('/pengajuan-lembur/store-lkh', [LembureController::class, 'store_lkh'])->name('lembure.pengajuan-lembur.store-lkh');
        Route::patch('/pengajuan-lembur/update/{idLembur}', [LembureController::class, 'update'])->name('lembure.pengajuan-lembur.update');
    });
});

