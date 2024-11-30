<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Cutie\CutieController;
use App\Http\Controllers\Lembure\LembureController;
use App\Http\Controllers\MasterData\AkunController;
use App\Http\Controllers\MasterData\GrupController;
use App\Http\Controllers\MasterData\EventController;
use App\Http\Controllers\MasterData\SeksiController;
use App\Http\Controllers\MasterData\DivisiController;
use App\Http\Controllers\MasterData\ExportController;
use App\Http\Controllers\MasterData\PosisiController;
use App\Http\Controllers\MasterData\JabatanController;
use App\Http\Controllers\MasterData\KontrakController;
use App\Http\Controllers\MasterData\KaryawanController;
use App\Http\Controllers\MasterData\TemplateController;
use App\Http\Controllers\MasterData\TurnoverController;
use App\Http\Controllers\MasterData\DashboardController;
use App\Http\Controllers\MasterData\DepartemenController;
use App\Http\Controllers\MasterData\OrganisasiController;


Auth::routes();
Route::get('/', function () {
    return redirect('/login');
});

/** MASTER DATA - AJAX */
Route::get('/master-data/posisi/get-data-by-jabatan/{idJabatan}',[PosisiController::class, 'get_data_by_jabatan']);
Route::get('/master-data/posisi/get-data-by-posisi/{idPosisi}',[PosisiController::class, 'get_data_by_posisi']);
Route::get('/master-data/posisi/get-data-all-posisi',[PosisiController::class, 'get_data_all_posisi']);
Route::get('/master-data/posisi/get-data-jabatan-by-posisi/{idPosisi}',[PosisiController::class, 'get_data_jabatan_by_posisi']); 
Route::get('/master-data/posisi/get-data-jabatan-by-posisi-edit/{idPosisi}/{myPosisi}',[PosisiController::class, 'get_data_jabatan_by_posisi_edit']); 
Route::post('/master-data/posisi/get-data-parent',[PosisiController::class, 'get_data_parent']); 
Route::post('/master-data/posisi/get-data-posisi',[PosisiController::class, 'get_data_posisi']); 
Route::get('/master-data/posisi/get-data-parent-edit/{idParent}',[PosisiController::class, 'get_data_parent_edit']); 

Route::get('/master-data/organisasi/get-data-organisasi',[OrganisasiController::class, 'get_data_organisasi']); 

Route::post('/master-data/grup/get-data-grup',[GrupController::class, 'get_data_grup']); 
Route::get('/master-data/grup/get-data-all-grup',[GrupController::class, 'get_data_all_grup']); 
Route::post('/master-data/karyawan/get-data-user',[KaryawanController::class, 'get_data_user']); 
Route::post('/master-data/karyawan/get-data-karyawan',[KaryawanController::class, 'get_data_karyawan']); 
Route::get('/master-data/karyawan/get-data-detail-karyawan/{idKaryawan}',[KaryawanController::class, 'get_data_detail_karyawan']); 

Route::get('/master-data/akun/get-data-detail-akun/{idAkun}',[AkunController::class, 'get_data_detail_akun']); 

Route::get('/master-data/kontrak/get-data-list-kontrak/{idKaryawan}',[KontrakController::class, 'get_data_list_kontrak']); 
Route::get('/master-data/kontrak/download-kontrak-kerja/{idKontrak}',[KontrakController::class, 'download_kontrak_kerja']); 
Route::get('/master-data/kontrak/get-data-detail-kontrak/{idKontrak}',[KontrakController::class, 'get_data_detail_kontrak']); 

Route::get('/cutie/pengajuan-cuti/get-data-jenis-cuti-khusus',[CutieController::class, 'get_data_jenis_cuti_khusus']); 
Route::get('/cutie/pengajuan-cuti/get-data-detail-cuti/{idCuti}',[CutieController::class, 'get_data_detail_cuti']); 
Route::get('/cutie/member-cuti/get-karyawan-pengganti/{idKaryawan}',[CutieController::class, 'get_karyawan_pengganti']);
Route::get('/cutie/dashboard-cuti/get-data-cuti-calendar',[CutieController::class, 'get_data_cutie_calendar']);
Route::get('/cutie/dashboard-cuti/get-data-cuti-detail-chart',[CutieController::class, 'get_data_cuti_detail_chart']);
Route::get('/cutie/dashboard-cuti/get-data-jenis-cuti-monthly-chart',[CutieController::class, 'get_data_jenis_cuti_monthly_chart']);
Route::get('/cutie/setting-cuti/get-data-detail-jenis-cuti/{idJenisCuti}',[CutieController::class, 'get_data_detail_jenis_cuti']);

Route::post('/lembure/pengajuan-lembur/get-data-karyawan-lembur',[LembureController::class, 'get_data_karyawan_lembur']); 
Route::get('/lembure/pengajuan-lembur/get-data-karyawan-lembur',[LembureController::class, 'get_karyawan_lembur']); 
Route::get('/lembure/pengajuan-lembur/get-data-lembur/{idLembur}',[LembureController::class, 'get_data_lembur']); 
Route::post('/lembure/dashboard-lembur/get-monthly-lembur-per-departemen',[LembureController::class, 'get_monthly_lembur_per_departemen']); 
Route::post('/lembure/dashboard-lembur/get-weekly-lembur-per-departemen',[LembureController::class, 'get_weekly_lembur_per_departemen']); 
Route::post('/lembure/dashboard-lembur/get-current-month-lembur-per-departemen',[LembureController::class, 'get_current_month_lembur_per_departemen']); 
Route::get('/get-approval-lembur-notification', [HomeController::class, 'get_approval_lembur_notification'])->middleware('lembure');
Route::get('/get-planned-pengajuan-lembur-notification', [HomeController::class, 'get_planned_pengajuan_lembur_notification'])->middleware('lembure');
Route::get('/lembure/pengajuan-lembur/get-attachment-lembur/{idLembur}',[LembureController::class, 'get_attachment_lembur']);

Route::group(['middleware' => ['auth', 'notifikasi', 'lembure']], function () {
    // MENU UTAMA
    Route::get('/home', [HomeController::class, 'index'])->name('root');
    Route::get('/get-notification', [HomeController::class, 'get_notification']);
    Route::get('/get-pengajuan-cuti-notification', [HomeController::class, 'get_pengajuan_cuti_notification']);
    Route::get('/get-member-cuti-notification', [HomeController::class, 'get_member_cuti_notification']);
    Route::get('/get-list-cuti-notification', [HomeController::class, 'get_list_cuti_notification']);
    Route::post('/export-slip-lembur', [HomeController::class, 'export_slip_lembur'])->name('home.export-slip-lembur');

    /** MASTER DATA FEATURE */
    Route::group(['prefix' => 'master-data'], function () {
        /** MASTER DATA - DASHBOARD */
        Route::get('/dashboard',[DashboardController::class, 'index'])->name('master-data.dashboard');
        Route::get('/dashboard/get-data-karyawan-dashboard',[DashboardController::class, 'get_data_karyawan_dashboard']);
        Route::get('/dashboard/get-data-turnover-monthly-dashboard',[DashboardController::class, 'get_data_turnover_monthly_dashboard']);
        Route::get('/dashboard/get-data-turnover-detail-monthly-dashboard',[DashboardController::class, 'get_data_turnover_detail_monthly_dashboard']);
        Route::get('/dashboard/get-data-kontrak-progress-dashboard',[DashboardController::class, 'get_data_kontrak_progress_dashboard']);
        Route::get('/dashboard/get-data-keluar-masuk-karyawan-dashboard',[DashboardController::class, 'get_data_keluar_masuk_karyawan_dashboard']);
        Route::get('/dashboard/get-total-data-karyawan-by-status-karyawan-dashboard',[DashboardController::class, 'get_total_data_karyawan_by_status_karyawan_dashboard']);
        Route::get('/event/get-data-event-calendar',[EventController::class, 'get_data_event_calendar']);
        
        /** MASTER DATA - ORGANISASI */
        Route::group(['middleware' => ['role:personalia']], function () {

            Route::post('/organisasi/datatable', [OrganisasiController::class, 'datatable']);
            Route::get('/organisasi',[OrganisasiController::class, 'index'])->name('master-data.organisasi');
            Route::post('/organisasi/store',[OrganisasiController::class, 'store'])->name('master-data.organisasi.store');
            Route::delete('/organisasi/delete/{idOrganisasi}', [OrganisasiController::class, 'delete'])->name('master-data.organisasi.delete');
            Route::patch('/organisasi/update/{idOrganisasi}', [OrganisasiController::class, 'update'])->name('master-data.organisasi.update');
        
            /** MASTER DATA - DIVISI */
            Route::post('/divisi/datatable', [DivisiController::class, 'datatable']);
            Route::get('/divisi',[DivisiController::class, 'index'])->name('master-data.divisi');
            Route::post('/divisi/store',[DivisiController::class, 'store'])->name('master-data.divisi.store');
            Route::delete('/divisi/delete/{idDivisi}', [DivisiController::class, 'delete'])->name('master-data.divisi.delete');
            Route::patch('/divisi/update/{idDivisi}', [DivisiController::class, 'update'])->name('master-data.divisi.update');

            /** MASTER DATA - DEPARTEMEN */
            Route::post('/departemen/datatable', [DepartemenController::class, 'datatable']);
            Route::get('/departemen',[DepartemenController::class, 'index'])->name('master-data.departemen');
            Route::post('/departemen/store',[DepartemenController::class, 'store'])->name('master-data.departemen.store');
            Route::delete('/departemen/delete/{idDepartemen}', [DepartemenController::class, 'delete'])->name('master-data.departemen.delete');
            Route::patch('/departemen/update/{idDepartemen}', [DepartemenController::class, 'update'])->name('master-data.departemen.update');

            /** MASTER DATA - SEKSI */
            Route::post('/seksi/datatable', [SeksiController::class, 'datatable']);
            Route::get('/seksi',[SeksiController::class, 'index'])->name('master-data.seksi');
            Route::post('/seksi/store',[SeksiController::class, 'store'])->name('master-data.seksi.store');
            Route::delete('/seksi/delete/{idSeksi}', [SeksiController::class, 'delete'])->name('master-data.seksi.delete');
            Route::patch('/seksi/update/{idSeksi}', [SeksiController::class, 'update'])->name('master-data.seksi.update');

            /** MASTER DATA - GRUP */
            Route::post('/grup/datatable', [GrupController::class, 'datatable']);
            Route::get('/grup',[GrupController::class, 'index'])->name('master-data.grup');
            Route::post('/grup/store',[GrupController::class, 'store'])->name('master-data.grup.store');
            Route::delete('/grup/delete/{idGrup}', [GrupController::class, 'delete'])->name('master-data.grup.delete');
            Route::patch('/grup/update/{idGrup}', [GrupController::class, 'update'])->name('master-data.grup.update');

            /** MASTER DATA - JABATAN */
            Route::post('/jabatan/datatable', [JabatanController::class, 'datatable']);
            Route::get('/jabatan',[JabatanController::class, 'index'])->name('master-data.jabatan');
            Route::post('/jabatan/store',[JabatanController::class, 'store'])->name('master-data.jabatan.store');
            Route::delete('/jabatan/delete/{idJabatan}', [JabatanController::class, 'delete'])->name('master-data.jabatan.delete');
            Route::patch('/jabatan/update/{idJabatan}', [JabatanController::class, 'update'])->name('master-data.jabatan.update');

            /** MASTER DATA - POSISI */
            Route::post('/posisi/datatable', [PosisiController::class, 'datatable']);
            Route::get('/posisi',[PosisiController::class, 'index'])->name('master-data.posisi');
            Route::post('/posisi/store',[PosisiController::class, 'store'])->name('master-data.posisi.store');
            Route::delete('/posisi/delete/{idPosisi}', [PosisiController::class, 'delete'])->name('master-data.posisi.delete');
            Route::patch('/posisi/update/{idPosisi}', [PosisiController::class, 'update'])->name('master-data.posisi.update');

            /** MASTER DATA - KARYAWAN */
            Route::post('/karyawan/datatable', [KaryawanController::class, 'datatable']);
            Route::get('/karyawan',[KaryawanController::class, 'index'])->name('master-data.karyawan');
            Route::post('/karyawan/store',[KaryawanController::class, 'store'])->name('master-data.karyawan.store');
            Route::post('/karyawan/upload-karyawan',[KaryawanController::class, 'upload_karyawan'])->name('master-data.karyawan.upload-karyawan');
            Route::delete('/karyawan/delete/{idKaryawan}', [KaryawanController::class, 'delete'])->name('master-data.karyawan.delete');
            Route::patch('/karyawan/update/{idKaryawan}', [KaryawanController::class, 'update'])->name('master-data.karyawan.update');
            Route::post('/akun/store-or-update',[AkunController::class, 'store_or_update'])->name('master-data.akun.storeUpdate');
            
            /** MASTER DATA - KONTRAK */
            Route::post('/kontrak/datatable', [KontrakController::class, 'datatable']);
            Route::get('/kontrak',[KontrakController::class, 'index'])->name('master-data.kontrak');
            Route::post('/kontrak/store',[KontrakController::class, 'store'])->name('master-data.kontrak.store');
            Route::delete('/kontrak/delete/{idKontrak}', [KontrakController::class, 'delete'])->name('master-data.kontrak.delete');
            Route::patch('/kontrak/update/{idKontrak}', [KontrakController::class, 'update'])->name('master-data.kontrak.update');
            // Tidak Dipakai Lagi
            // Route::post('/kontrak/store-or-update',[KontrakController::class, 'store_or_update'])->name('master-data.kontrak.storeUpdate');
            Route::post('/kontrak/upload-kontrak/{type}/{idKontrak}',[KontrakController::class, 'upload_kontrak'])->name('master-data.kontrak.upload');
            Route::post('/kontrak/upload-data-kontrak',[KontrakController::class, 'upload_data_kontrak'])->name('master-data.kontrak.upload-data-kontrak');
            Route::post('/kontrak/done/{idKontrak}',[KontrakController::class, 'done_kontrak'])->name('master-data.kontrak.done');

            /** MASTER DATA - EXPORT */
            Route::get('/export',[ExportController::class, 'index'])->name('master-data.export');
            Route::post('/export/export-master-data',[ExportController::class, 'export_master_data'])->name('master-data.export.master-data');
            Route::post('/export/export-kontrak',[ExportController::class, 'export_kontrak'])->name('master-data.export.kontrak');

            /** MASTER DATA - TURNOVER */
            Route::post('/turnover/datatable', [TurnoverController::class, 'datatable']);
            Route::get('/turnover',[TurnoverController::class, 'index'])->name('master-data.turnover');
            Route::post('/turnover/store',[TurnoverController::class, 'store'])->name('master-data.turnover.store');

            /** MASTER DATA - TEMPLATE */
            Route::post('/template/datatable', [TemplateController::class, 'datatable']);
            Route::get('/template',[TemplateController::class, 'index'])->name('master-data.template');
            Route::post('/template/store',[TemplateController::class, 'store'])->name('master-data.template.store');
            Route::delete('/template/delete/{idTemplate}', [TemplateController::class, 'delete'])->name('master-data.template.delete');
            Route::patch('/template/update/{idTemplate}', [TemplateController::class, 'update'])->name('master-data.template.update');

            /** MASTER DATA - KALENDER PERUSAHAAN*/
            Route::post('/event/datatable', [EventController::class, 'datatable']);
            Route::get('/event',[EventController::class, 'index'])->name('master-data.event');
            Route::post('/event/store',[EventController::class, 'store'])->name('master-data.event.store');
            Route::delete('/event/delete/{idEvent}', [EventController::class, 'delete'])->name('master-data.event.delete');
        });
    });

    /** CUTIE */
    Route::group(['prefix' => 'cutie'], function () {

        /** DASHBOARD */
        Route::get('/dashboard', [CutieController::class, 'index'])->middleware('role:personalia|atasan')->name('cutie.dashboard');

        /** EXPORT */
        Route::get('/export',[CutieController::class, 'export_cuti_view'])->name('cutie.export');
        Route::post('/export/export-cuti',[CutieController::class, 'export_cuti'])->name('cutie.export.cuti');

        /** PERSONAL CUTI */
        
        Route::group(['middleware' => ['role:atasan|member']], function () {
            Route::post('/pengajuan-cuti-datatable', [CutieController::class, 'pengajuan_cuti_datatable']);
            Route::get('/pengajuan-cuti',[CutieController::class, 'pengajuan_cuti_view'])->name('cutie.pengajuan-cuti');
            Route::post('/pengajuan-cuti/store',[CutieController::class, 'store'])->name('cutie.pengajuan-cuti.store');
            Route::delete('/pengajuan-cuti/delete/{idCuti}',[CutieController::class, 'delete'])->name('cutie.pengajuan-cuti.delete');
            Route::patch('/pengajuan-cuti/update/{idCuti}',[CutieController::class, 'update'])->name('cutie.pengajuan-cuti.update');
            Route::patch('/pengajuan-cuti/cancel/{idCuti}',[CutieController::class, 'cancel'])->name('cutie.pengajuan-cuti.cancel');
            Route::patch('/pengajuan-cuti/mulai-cuti/{idCuti}',[CutieController::class, 'mulai_cuti'])->name('cutie.pengajuan-cuti.mulai-cuti');
            Route::patch('/pengajuan-cuti/selesai-cuti/{idCuti}',[CutieController::class, 'selesai_cuti'])->name('cutie.pengajuan-cuti.selesai-cuti');
        });

        /** MEMBER CUTI */
        Route::group(['middleware' => ['role:atasan']], function () {
            Route::post('/member-cuti-datatable', [CutieController::class, 'member_cuti_datatable']);
            Route::get('/member-cuti',[CutieController::class, 'member_cuti_view'])->name('cutie.member-cuti');
            Route::post('/member-cuti/store',[CutieController::class, 'store'])->name('cutie.member-cuti.store');
            Route::delete('/member-cuti/delete/{idCuti}',[CutieController::class, 'delete'])->name('cutie.member-cuti.delete');
            Route::patch('/member-cuti/update/{idCuti}',[CutieController::class, 'update'])->name('cutie.member-cuti.update');
            Route::patch('/member-cuti/update-karyawan-pengganti/{idCuti}',[CutieController::class, 'update_karyawan_pengganti'])->name('cutie.member-cuti.update-karyawan-pengganti');
            Route::patch('/member-cuti/reject/{idCuti}',[CutieController::class, 'reject'])->name('cutie.member-cuti.reject');
        });
        Route::patch('/member-cuti/update-dokumen-cuti/{idCuti}',[CutieController::class, 'update_dokumen_cuti'])->name('cutie.member-cuti.update-document-cuti');

        /** PERSONALIA CUTI */
        Route::group(['middleware' => ['role:personalia']], function () {
            Route::post('/personalia-cuti-datatable', [CutieController::class, 'personalia_cuti_datatable']);
            Route::get('/personalia-cuti',[CutieController::class, 'personalia_cuti_view'])->name('cutie.personalia-cuti');
            Route::delete('/personalia-cuti/delete/{idCuti}',[CutieController::class, 'delete'])->name('cutie.personalia-cuti.delete');
            Route::patch('/personalia-cuti/cancel/{idCuti}',[CutieController::class, 'cancel'])->name('cutie.personalia-cuti.cancel');
        
            /** SETTING CUTI */
            Route::post('/setting-cuti-datatable', [CutieController::class, 'setting_cuti_datatable']);
            Route::get('/setting-cuti',[CutieController::class, 'setting_cuti_view'])->name('cutie.setting-cuti');
            Route::delete('/setting-cuti/delete/{idCuti}',[CutieController::class, 'delete_jenis_cuti'])->name('cutie.setting-cuti.delete');
            Route::patch('/setting-cuti/update/{idCuti}',[CutieController::class, 'update_jenis_cuti'])->name('cutie.setting-cuti.update');
            Route::post('/setting-cuti/store',[CutieController::class, 'store_jenis_cuti'])->name('cutie.setting-cuti.store');

            /** BYPASS CUTI */
            Route::get('/bypass-cuti',[CutieController::class, 'bypass_cuti_view'])->name('cutie.bypass-cuti');
            Route::post('/bypass-cuti/store',[CutieController::class, 'bypass_store'])->name('cutie.bypass-cuti.store');
        });
     });

     Route::group(['prefix' => 'lembure'], function () {

        // DASHBOARD
        Route::get('/dashboard', [LembureController::class, 'index'])->name('lembure.dashboard')->middleware('role:personalia|atasan');

        // LEADERBOARD LEMBUR
        Route::group(['middleware' => ['role:atasan|personalia']], function () {
            Route::get('/detail-lembur', [LembureController::class, 'detail_lembur_view'])->name('lembure.detail-lembur');
            Route::post('/detail-lembur-datatable', [LembureController::class, 'detail_lembur_datatable']);
            Route::post('/detail-lembur/get-leaderboard-user-monthly',[LembureController::class, 'get_leaderboard_user_monthly']); 
        });

        Route::group(['middleware' => ['role:atasan|member']], function () {
        // PENGAJUAN LEMBUR (LEADER)
            Route::get('/pengajuan-lembur', [LembureController::class, 'pengajuan_lembur_view'])->name('lembure.pengajuan-lembur');
            Route::post('/pengajuan-lembur-datatable', [LembureController::class, 'pengajuan_lembur_datatable']);
            Route::post('/pengajuan-lembur/store', [LembureController::class, 'store'])->name('lembure.pengajuan-lembur.store');
            Route::delete('pengajuan-lembur/delete/{idLembur}',[LembureController::class, 'delete'])->name('lembure.pengajuan-lembur.delete');
            Route::patch('/pengajuan-lembur/update/{idLembur}', [LembureController::class, 'update'])->name('lembure.pengajuan-lembur.update');
            Route::patch('/pengajuan-lembur/done/{idLembur}', [LembureController::class, 'done'])->name('lembure.pengajuan-lembur.done');
            Route::post('/pengajuan-lembur/store-lkh', [LembureController::class, 'store_lkh'])->name('lembure.pengajuan-lembur.store-lkh');
        });

        Route::group(['middleware' => ['role:atasan|personalia']], function () {
            // APPROVAL LEMBUR (CHECK)
            Route::get('/approval-lembur', [LembureController::class, 'approval_lembur_view'])->name('lembure.approval-lembur');
            Route::post('/approval-lembur-datatable', [LembureController::class, 'approval_lembur_datatable']);
            Route::post('/approval-lembur/get-calculation-durasi-and-nominal-lembur/{idDetailLembur}', [LembureController::class, 'get_calculation_durasi_and_nominal_lembur']);
            Route::patch('/approval-lembur/rejected/{idLembur}', [LembureController::class, 'rejected'])->name('lembure.approval-lembur.rejected');
            Route::patch('/approval-lembur/checked/{idLembur}', [LembureController::class, 'checked'])->name('lembure.approval-lembur.checked');
            Route::patch('/approval-lembur/approved/{idLembur}', [LembureController::class, 'approved'])->name('lembure.approval-lembur.approved');
            Route::patch('/approval-lembur/legalized/{idLembur}', [LembureController::class, 'legalized'])->name('lembure.approval-lembur.legalized');
            Route::patch('/approval-lembur/checked-aktual/{idLembur}', [LembureController::class, 'checked_aktual'])->name('lembure.approval-lembur.checked-aktual');
            Route::patch('/approval-lembur/approved-aktual/{idLembur}', [LembureController::class, 'approved_aktual'])->name('lembure.approval-lembur.approved-aktual');
            Route::patch('/approval-lembur/legalized-aktual/{idLembur}', [LembureController::class, 'legalized_aktual'])->name('lembure.approval-lembur.legalized-aktual');
        });

        Route::group(['middleware' => ['role:personalia']], function () {
            // Setting Upah Lembur
            Route::get('/setting-upah-lembur', [LembureController::class, 'setting_upah_lembur_view'])->name('lembure.setting-upah-lembur');
            Route::post('/setting-upah-lembur-datatable', [LembureController::class, 'setting_upah_lembur_datatable']);
            Route::patch('/setting-upah-lembur/update', [LembureController::class, 'update_setting_upah_lembur'])->name('lembure.setting-upah-lembur.update');
            Route::post('/upload-upah-lembur-karyawan', [LembureController::class, 'upload_upah_lembur_karyawan'])->name('lembure.setting-upah-lembur.upload');

            // Setting Lembur
            Route::get('/setting-lembur', [LembureController::class, 'setting_lembur_view'])->name('lembure.setting-lembur');
            Route::patch('/setting-lembur/update', [LembureController::class, 'update_setting_lembur'])->name('lembure.setting-lembur.update');

            // Setting Gaji Departemen
            Route::get('/setting-gaji-departemen', [LembureController::class, 'setting_gaji_departemen_view'])->name('lembure.setting-gaji-departemen');
            Route::post('/setting-gaji-departemen-datatable', [LembureController::class, 'setting_gaji_departemen_datatable']);
            Route::post('/setting-gaji-departemen/store', [LembureController::class, 'store_setting_gaji_departemen'])->name('lembure.setting-gaji-departemen.store');
            Route::patch('/setting-gaji-departemen/update', [LembureController::class, 'update_setting_gaji_departemen'])->name('lembure.setting-gaji-departemen.update');

            // Export Report Lembur
            Route::get('/export-report-lembur', [LembureController::class, 'export_report_lembur_view'])->name('lembure.export-report-lembur');
            Route::post('/export-report-lembur/rekap-lembur-perbulan', [LembureController::class, 'export_rekap_lembur_perbulan'])->name('lembure.export-report-lembur.rekap-lembur-perbulan');
            Route::post('/export-report-lembur/slip-lembur-perbulan', [LembureController::class, 'export_slip_lembur_perbulan'])->name('lembure.export-report-lembur.export-slip-lembur-perbulan');
        });
     });
});



