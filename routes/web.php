<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Utils\QrController;
use App\Http\Controllers\Cutie\CutieController;
use App\Http\Controllers\Izine\IzineController;
use App\Http\Controllers\Izine\SakiteController;
use App\Http\Controllers\Lembure\LembureController;
use App\Http\Controllers\MasterData\AkunController;
use App\Http\Controllers\MasterData\GrupController;
use App\Http\Controllers\StockOpname\StoController;
use App\Http\Controllers\Attendance\RekapController;
use App\Http\Controllers\MasterData\EventController;
use App\Http\Controllers\MasterData\SeksiController;
use App\Http\Controllers\Attendance\DeviceController;
use App\Http\Controllers\MasterData\DivisiController;
use App\Http\Controllers\MasterData\ExportController;
use App\Http\Controllers\MasterData\PosisiController;
use App\Http\Controllers\Security\SecurityController;
use App\Http\Controllers\Utils\DeleteQrImgController;
use App\Http\Controllers\Attendance\ScanlogController;
use App\Http\Controllers\Attendance\WebhookController;
use App\Http\Controllers\MasterData\JabatanController;
use App\Http\Controllers\MasterData\KontrakController;
use App\Http\Controllers\Attendance\PresensiController;
use App\Http\Controllers\MasterData\KaryawanController;
use App\Http\Controllers\MasterData\TemplateController;
use App\Http\Controllers\MasterData\TurnoverController;
use App\Http\Controllers\MasterData\DashboardController;
use App\Http\Controllers\Attendance\ShiftgroupController;
use App\Http\Controllers\MasterData\DepartemenController;
use App\Http\Controllers\MasterData\OrganisasiController;
use App\Http\Controllers\StockOpname\StoReportController;
use App\Http\Controllers\Attendance\AttendanceGpsController;
use App\Http\Controllers\Attendance\LiveAttendanceController;
use App\Http\Controllers\KSK\AjaxController as KSKAjaxController;
use App\Http\Controllers\KSK\SettingController as KSKSettingController;
use App\Http\Controllers\KSK\ReleaseController as KSKReleaseController;
use App\Http\Controllers\TugasLuare\AjaxController as TLAjaxController;
use App\Http\Controllers\KSK\ApprovalController as KSKApprovalController;
use App\Http\Controllers\TugasLuare\ApprovalController as TLApprovalController;
use App\Http\Controllers\Attendance\ApprovalController as ATTApprovalController;
use App\Http\Controllers\TugasLuare\PengajuanController as TLPengajuanController;
use App\Http\Controllers\KSK\Cleareance\AjaxController as KSKAjaxCleareanceController;
use App\Http\Controllers\Attendance\DashboardController as AttendanceDashboardController;
use App\Http\Controllers\KSK\Cleareance\ReleaseController as KSKReleaseCleareanceController;
use App\Http\Controllers\KSK\Cleareance\ApprovalController as KSKApprovalCleareanceController;

Auth::routes();
Route::get('/', function () {
    return redirect('/login');
});

// HRIS
Route::group(['middleware' => ['auth']], function () {

    Route::get('/503/under-maintenance', function () {
        return view('maintenance-mode');
    })->name('under-maintenance');

    //Generate System
    // Route::get('/generate-lembur-harian', [LembureController::class, 'generate_lembur_harian']);
    Route::get('/generate-approval-cuti',[TestController::class, 'generate_approval_cuti']);
    Route::post('/generate-qrcode', QrController::class);
    Route::delete('/delete-qrcode-img', DeleteQrImgController::class);
    Route::get('/upload-karyawan', [TestController::class, 'upload_karyawan_view']);
    Route::get('/upload-pin', [TestController::class, 'upload_pin_view']);
    Route::post('/upload-pin/store', [TestController::class, 'upload_pin'])->name('upload-pin.store');
    Route::post('/upload-karyawan/store', [TestController::class, 'upload_karyawan'])->name('upload-karyawan.store');
    Route::get('/rekap-presensi', [TestController::class, 'test_rekap_presensi']);
    Route::get('/test', [TestController::class, 'test']);
    Route::get('/nevergiveup/{date}/{hour}/{minute}/{type}', [TestController::class, 'nevergiveup']);

    //WHATSAPP API
    Route::get('/send-whatsapp', [TestController::class, 'send_whatsapp_message_v2']);
    Route::get('/add-whatsapp-user', [TestController::class, 'add_whatsapp_user']);
    Route::get('/add-whatsapp-device', [TestController::class, 'add_whatsapp_device']);
    Route::get('/start-whatsapp-client', [TestController::class, 'start_whatsapp_client']);

    /** MASTER DATA - AJAX */
    Route::get('/master-data/posisi/get-data-by-jabatan/{idJabatan}', [PosisiController::class, 'get_data_by_jabatan']);
    Route::get('/master-data/posisi/get-data-by-posisi/{idPosisi}', [PosisiController::class, 'get_data_by_posisi']);
    Route::get('/master-data/posisi/get-data-all-posisi', [PosisiController::class, 'get_data_all_posisi']);
    Route::get('/master-data/posisi/get-data-jabatan-by-posisi/{idPosisi}', [PosisiController::class, 'get_data_jabatan_by_posisi']);
    Route::get('/master-data/posisi/get-data-jabatan-by-posisi-edit/{idPosisi}/{myPosisi}', [PosisiController::class, 'get_data_jabatan_by_posisi_edit']);
    Route::post('/master-data/posisi/get-data-parent', [PosisiController::class, 'get_data_parent']);
    Route::post('/master-data/posisi/get-data-posisi', [PosisiController::class, 'get_data_posisi']);
    Route::get('/master-data/posisi/get-data-parent-edit/{idParent}', [PosisiController::class, 'get_data_parent_edit']);

    Route::get('/master-data/organisasi/get-data-organisasi', [OrganisasiController::class, 'get_data_organisasi']);

    Route::post('/master-data/grup/get-data-grup', [GrupController::class, 'get_data_grup']);
    Route::get('/master-data/grup/get-data-all-grup', [GrupController::class, 'get_data_all_grup']);
    Route::get('/master-data/grup/get-data-grup-pattern/{idGrupPattern}', [GrupController::class, 'get_data_grup_pattern']);
    Route::post('/master-data/karyawan/get-data-user', [KaryawanController::class, 'get_data_user']);
    Route::post('/master-data/karyawan/get-data-karyawan', [KaryawanController::class, 'get_data_karyawan']);
    Route::get('/master-data/karyawan/get-data-detail-karyawan/{idKaryawan}', [KaryawanController::class, 'get_data_detail_karyawan']);

    Route::get('/master-data/akun/get-data-detail-akun/{idAkun}', [AkunController::class, 'get_data_detail_akun']);

    Route::get('/master-data/kontrak/get-data-list-kontrak/{idKaryawan}', [KontrakController::class, 'get_data_list_kontrak']);
    Route::get('/master-data/kontrak/download-kontrak-kerja/{idKontrak}', [KontrakController::class, 'download_kontrak_kerja']);
    Route::get('/master-data/kontrak/get-data-detail-kontrak/{idKontrak}', [KontrakController::class, 'get_data_detail_kontrak']);

    Route::get('/cutie/pengajuan-cuti/get-data-jenis-cuti-khusus', [CutieController::class, 'get_data_jenis_cuti_khusus']);
    Route::get('/cutie/pengajuan-cuti/get-data-detail-cuti/{idCuti}', [CutieController::class, 'get_data_detail_cuti']);
    Route::get('/cutie/member-cuti/get-karyawan-pengganti/{idKaryawan}', [CutieController::class, 'get_karyawan_pengganti']);
    Route::get('/cutie/dashboard-cuti/get-data-cuti-calendar', [CutieController::class, 'get_data_cutie_calendar']);
    Route::get('/cutie/dashboard-cuti/get-data-cuti-detail-chart', [CutieController::class, 'get_data_cuti_detail_chart']);
    Route::get('/cutie/dashboard-cuti/get-data-jenis-cuti-monthly-chart', [CutieController::class, 'get_data_jenis_cuti_monthly_chart']);
    Route::get('/cutie/setting-cuti/get-data-detail-jenis-cuti/{idJenisCuti}', [CutieController::class, 'get_data_detail_jenis_cuti']);
    Route::post('/cutie/bypass-cuti/get-karyawan-cuti', [CutieController::class, 'get_karyawan_cuti']);

    Route::post('/lembure/pengajuan-lembur/get-data-karyawan-lembur', [LembureController::class, 'get_data_karyawan_lembur']);
    Route::post('/lembure/pengajuan-lembur/get-data-karyawan-bypass-lembur', [LembureController::class, 'get_data_karyawan_bypass_lembur']);
    Route::get('/lembure/pengajuan-lembur/get-data-karyawan-lembur', [LembureController::class, 'get_karyawan_lembur']);
    Route::get('/lembure/pengajuan-lembur/get-data-lembur/{idLembur}', [LembureController::class, 'get_data_lembur']);
    Route::post('/lembure/dashboard-lembur/get-monthly-lembur-per-departemen', [LembureController::class, 'get_monthly_lembur_per_departemen']);
    Route::post('/lembure/dashboard-lembur/get-weekly-lembur-per-departemen', [LembureController::class, 'get_weekly_lembur_per_departemen']);
    Route::post('/lembure/dashboard-lembur/get-current-month-lembur-per-departemen', [LembureController::class, 'get_current_month_lembur_per_departemen']);
    Route::get('/get-approval-lembur-notification', [HomeController::class, 'get_approval_lembur_notification'])->middleware('lembure');
    Route::get('/get-review-lembur-notification', [HomeController::class, 'get_review_lembur_notification'])->middleware('lembure');
    Route::get('/get-planned-pengajuan-lembur-notification', [HomeController::class, 'get_planned_pengajuan_lembur_notification'])->middleware('lembure');
    Route::get('/lembure/pengajuan-lembur/get-attachment-lembur/{idLembur}', [LembureController::class, 'get_attachment_lembur']);
    Route::post('/lembure/review-lembur/get-review-lembur-detail', [LembureController::class, 'get_review_lembur_detail']);

    Route::get('/izine/pengajuan-izin/get-data-izin/{idIzin}', [IzineController::class, 'get_data_izin']);
    Route::get('/izine/lapor-skd/get-data-sakit/{idSakit}', [SakiteController::class, 'get_data_sakit']);
    Route::get('/izine/log-book-izin/get-qrcode-detail-izin/{idIzin}', [IzineController::class, 'get_qrcode_detail_izin']);

    Route::get('/attendance/device/get-all-device', [DeviceController::class, 'get_all_device']);
    Route::post('/attendance/presensi/get-summary-presensi', [PresensiController::class, 'get_summary_presensi_html']);
    Route::post('/attendance/presensi/get-detail-presensi', [PresensiController::class, 'get_detail_presensi']);
    Route::get('/attendance/shift-group/get-data-grup-pattern/{idGrupPattern}', [ShiftgroupController::class, 'get_data_grup_pattern']);

    //AJAX CONTROLLER
    Route::group(['prefix' => 'ajax'], function () {
        Route::post('/tugasluare/pengajuan/select-get-data-karyawan', [TLAjaxController::class, 'select_get_data_karyawan']);
        Route::get('/tugasluare/pengajuan/select-get-data-all-karyawan', [TLAjaxController::class, 'select_get_data_all_karyawan']);
        Route::get('/tugasluare/pengajuan/get-data-pengikut/{idTugasLuar}', [TLAjaxController::class, 'get_data_pengikut']);
        Route::get('/tugasluare/pengajuan/notification', [HomeController::class, 'get_pengajuan_tugasluar_notification'])->middleware('tugasluare');
        Route::get('/tugasluare/approval/notification', [HomeController::class, 'get_approval_tugasluar_notification'])->middleware('tugasluare');
    });

    //LIVE ATTENDANCE
    Route::get('/live-attendance', [LiveAttendanceController::class, 'index'])->name('attendance.live-attendance');
    Route::get('/test-live-attendance', [LiveAttendanceController::class, 'test']);
    Route::post('/get-live-attendance-chart', [LiveAttendanceController::class, 'get_live_attendance_chart']);
    Route::post('/live-attendance/datatable', [LiveAttendanceController::class, 'datatable']);

    // KSK
    Route::group(['prefix' => 'ajax'], function () {
        Route::get('/ksk/get-ksk-notification', [HomeController::class, 'get_ksk_notification']);
    });
});


Route::group(['middleware' => ['auth', 'notifikasi']], function () {
    // MENU UTAMA

    //HOME CONTROLLER
    Route::get('/home', [HomeController::class, 'index'])->name('root')->middleware(['lembure', 'izine', 'tugasluare', 'ksk']);
    Route::get('/get-notification', [HomeController::class, 'get_notification']);
    Route::get('/get-pengajuan-cuti-notification', [HomeController::class, 'get_pengajuan_cuti_notification']);
    Route::get('/get-member-cuti-notification', [HomeController::class, 'get_member_cuti_notification']);
    Route::get('/get-list-cuti-notification', [HomeController::class, 'get_list_cuti_notification']);
    Route::get('/get-pengajuan-izin-notification', [HomeController::class, 'get_pengajuan_izin_notification']);
    Route::get('/get-approval-izin-notification', [HomeController::class, 'get_approval_izin_notification']);
    Route::get('/get-lapor-skd-notification', [HomeController::class, 'get_lapor_skd_notification']);
    Route::get('/get-approval-skd-notification', [HomeController::class, 'get_approval_skd_notification']);
    Route::post('/export-slip-lembur', [HomeController::class, 'export_slip_lembur'])->name('home.export-slip-lembur');

    /** MASTER DATA FEATURE */
    Route::group(['prefix' => 'master-data'], function () {
        /** MASTER DATA - DASHBOARD */
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('master-data.dashboard');
        Route::get('/dashboard/get-data-karyawan-dashboard', [DashboardController::class, 'get_data_karyawan_dashboard']);
        Route::get('/dashboard/get-data-turnover-monthly-dashboard', [DashboardController::class, 'get_data_turnover_monthly_dashboard']);
        Route::get('/dashboard/get-data-turnover-detail-monthly-dashboard', [DashboardController::class, 'get_data_turnover_detail_monthly_dashboard']);
        Route::get('/dashboard/get-data-kontrak-progress-dashboard', [DashboardController::class, 'get_data_kontrak_progress_dashboard']);
        Route::get('/dashboard/get-data-keluar-masuk-karyawan-dashboard', [DashboardController::class, 'get_data_keluar_masuk_karyawan_dashboard']);
        Route::get('/dashboard/get-total-data-karyawan-by-status-karyawan-dashboard', [DashboardController::class, 'get_total_data_karyawan_by_status_karyawan_dashboard']);
        Route::get('/event/get-data-event-calendar', [EventController::class, 'get_data_event_calendar']);

        /** MASTER DATA - ORGANISASI */
        Route::group(['middleware' => ['role:personalia']], function () {

            Route::post('/organisasi/datatable', [OrganisasiController::class, 'datatable']);
            Route::get('/organisasi', [OrganisasiController::class, 'index'])->name('master-data.organisasi');
            Route::post('/organisasi/store', [OrganisasiController::class, 'store'])->name('master-data.organisasi.store');
            Route::delete('/organisasi/delete/{idOrganisasi}', [OrganisasiController::class, 'delete'])->name('master-data.organisasi.delete');
            Route::patch('/organisasi/update/{idOrganisasi}', [OrganisasiController::class, 'update'])->name('master-data.organisasi.update');

            /** MASTER DATA - DIVISI */
            Route::post('/divisi/datatable', [DivisiController::class, 'datatable']);
            Route::get('/divisi', [DivisiController::class, 'index'])->name('master-data.divisi');
            Route::post('/divisi/store', [DivisiController::class, 'store'])->name('master-data.divisi.store');
            Route::delete('/divisi/delete/{idDivisi}', [DivisiController::class, 'delete'])->name('master-data.divisi.delete');
            Route::patch('/divisi/update/{idDivisi}', [DivisiController::class, 'update'])->name('master-data.divisi.update');

            /** MASTER DATA - DEPARTEMEN */
            Route::post('/departemen/datatable', [DepartemenController::class, 'datatable']);
            Route::get('/departemen', [DepartemenController::class, 'index'])->name('master-data.departemen');
            Route::post('/departemen/store', [DepartemenController::class, 'store'])->name('master-data.departemen.store');
            Route::delete('/departemen/delete/{idDepartemen}', [DepartemenController::class, 'delete'])->name('master-data.departemen.delete');
            Route::patch('/departemen/update/{idDepartemen}', [DepartemenController::class, 'update'])->name('master-data.departemen.update');

            /** MASTER DATA - SEKSI */
            Route::post('/seksi/datatable', [SeksiController::class, 'datatable']);
            Route::get('/seksi', [SeksiController::class, 'index'])->name('master-data.seksi');
            Route::post('/seksi/store', [SeksiController::class, 'store'])->name('master-data.seksi.store');
            Route::delete('/seksi/delete/{idSeksi}', [SeksiController::class, 'delete'])->name('master-data.seksi.delete');
            Route::patch('/seksi/update/{idSeksi}', [SeksiController::class, 'update'])->name('master-data.seksi.update');

            /** MASTER DATA - GRUP */
            Route::post('/grup/datatable', [GrupController::class, 'datatable']);
            Route::post('/grup/shift-pattern-datatable', [GrupController::class, 'shift_pattern_datatable']);
            Route::get('/grup', [GrupController::class, 'index'])->name('master-data.grup');
            Route::post('/grup/store', [GrupController::class, 'store'])->name('master-data.grup.store');
            Route::delete('/grup/delete/{idGrup}', [GrupController::class, 'delete'])->name('master-data.grup.delete');
            Route::patch('/grup/update/{idGrup}', [GrupController::class, 'update'])->name('master-data.grup.update');
            Route::post('/grup/store-shift-pattern', [GrupController::class, 'store_shift_pattern'])->name('master-data.grup.store-shift-pattern');
            Route::delete('/grup/delete-shift-pattern/{idGrupPattern}', [GrupController::class, 'delete_shift_pattern'])->name('master-data.grup.delete-shift-pattern');
            Route::patch('/grup/update-shift-pattern/{idGrupPattern}', [GrupController::class, 'update_shift_pattern'])->name('master-data.grup.update-shift-pattern');

            /** MASTER DATA - JABATAN */
            Route::post('/jabatan/datatable', [JabatanController::class, 'datatable']);
            Route::get('/jabatan', [JabatanController::class, 'index'])->name('master-data.jabatan');
            Route::post('/jabatan/store', [JabatanController::class, 'store'])->name('master-data.jabatan.store');
            Route::delete('/jabatan/delete/{idJabatan}', [JabatanController::class, 'delete'])->name('master-data.jabatan.delete');
            Route::patch('/jabatan/update/{idJabatan}', [JabatanController::class, 'update'])->name('master-data.jabatan.update');

            /** MASTER DATA - POSISI */
            Route::post('/posisi/datatable', [PosisiController::class, 'datatable']);
            Route::get('/posisi', [PosisiController::class, 'index'])->name('master-data.posisi');
            Route::post('/posisi/store', [PosisiController::class, 'store'])->name('master-data.posisi.store');
            Route::delete('/posisi/delete/{idPosisi}', [PosisiController::class, 'delete'])->name('master-data.posisi.delete');
            Route::patch('/posisi/update/{idPosisi}', [PosisiController::class, 'update'])->name('master-data.posisi.update');

            /** MASTER DATA - KARYAWAN */
            Route::post('/karyawan/datatable', [KaryawanController::class, 'datatable']);
            Route::get('/karyawan', [KaryawanController::class, 'index'])->name('master-data.karyawan');
            Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('master-data.karyawan.store');
            Route::post('/karyawan/upload-karyawan', [KaryawanController::class, 'upload_karyawan'])->name('master-data.karyawan.upload-karyawan');
            Route::delete('/karyawan/delete/{idKaryawan}', [KaryawanController::class, 'delete'])->name('master-data.karyawan.delete');
            Route::patch('/karyawan/update/{idKaryawan}', [KaryawanController::class, 'update'])->name('master-data.karyawan.update');
            Route::post('/akun/store-or-update', [AkunController::class, 'store_or_update'])->name('master-data.akun.storeUpdate');

            /** MASTER DATA - KONTRAK */
            Route::post('/kontrak/datatable', [KontrakController::class, 'datatable']);
            Route::get('/kontrak', [KontrakController::class, 'index'])->name('master-data.kontrak');
            Route::post('/kontrak/store', [KontrakController::class, 'store'])->name('master-data.kontrak.store');
            Route::delete('/kontrak/delete/{idKontrak}', [KontrakController::class, 'delete'])->name('master-data.kontrak.delete');
            Route::patch('/kontrak/update/{idKontrak}', [KontrakController::class, 'update'])->name('master-data.kontrak.update');
            // Tidak Dipakai Lagi
            // Route::post('/kontrak/store-or-update',[KontrakController::class, 'store_or_update'])->name('master-data.kontrak.storeUpdate');
            Route::post('/kontrak/upload-kontrak/{type}/{idKontrak}', [KontrakController::class, 'upload_kontrak'])->name('master-data.kontrak.upload');
            Route::post('/kontrak/upload-data-kontrak', [KontrakController::class, 'upload_data_kontrak'])->name('master-data.kontrak.upload-data-kontrak');
            Route::post('/kontrak/done/{idKontrak}', [KontrakController::class, 'done_kontrak'])->name('master-data.kontrak.done');

            /** MASTER DATA - EXPORT */
            Route::get('/export', [ExportController::class, 'index'])->name('master-data.export');
            Route::post('/export/export-master-data', [ExportController::class, 'export_master_data'])->name('master-data.export.master-data');
            Route::post('/export/export-kontrak', [ExportController::class, 'export_kontrak'])->name('master-data.export.kontrak');

            /** MASTER DATA - TURNOVER */
            Route::post('/turnover/datatable', [TurnoverController::class, 'datatable']);
            Route::get('/turnover', [TurnoverController::class, 'index'])->name('master-data.turnover');
            Route::post('/turnover/store', [TurnoverController::class, 'store'])->name('master-data.turnover.store');

            /** MASTER DATA - TEMPLATE */
            Route::post('/template/datatable', [TemplateController::class, 'datatable']);
            Route::get('/template', [TemplateController::class, 'index'])->name('master-data.template');
            Route::post('/template/store', [TemplateController::class, 'store'])->name('master-data.template.store');
            Route::delete('/template/delete/{idTemplate}', [TemplateController::class, 'delete'])->name('master-data.template.delete');
            Route::patch('/template/update/{idTemplate}', [TemplateController::class, 'update'])->name('master-data.template.update');

            /** MASTER DATA - KALENDER PERUSAHAAN*/
            Route::post('/event/datatable', [EventController::class, 'datatable']);
            Route::get('/event', [EventController::class, 'index'])->name('master-data.event');
            Route::post('/event/store', [EventController::class, 'store'])->name('master-data.event.store');
            Route::delete('/event/delete/{idEvent}', [EventController::class, 'delete'])->name('master-data.event.delete');
        });
    });

    /** CUTIE */
    Route::group(['prefix' => 'cutie'], function () {

        /** DASHBOARD */
        Route::get('/dashboard', [CutieController::class, 'index'])->middleware('role:personalia|atasan')->name('cutie.dashboard');

        /** EXPORT */
        Route::get('/export', [CutieController::class, 'export_cuti_view'])->name('cutie.export');
        Route::post('/export/export-cuti', [CutieController::class, 'export_cuti'])->name('cutie.export.cuti');

        /** PERSONAL CUTI */

        Route::group(['middleware' => ['role:atasan|member']], function () {
            Route::post('/pengajuan-cuti-datatable', [CutieController::class, 'pengajuan_cuti_datatable']);
            Route::get('/pengajuan-cuti', [CutieController::class, 'pengajuan_cuti_view'])->name('cutie.pengajuan-cuti');
            Route::post('/pengajuan-cuti/store', [CutieController::class, 'store'])->name('cutie.pengajuan-cuti.store');
            Route::delete('/pengajuan-cuti/delete/{idCuti}', [CutieController::class, 'delete'])->name('cutie.pengajuan-cuti.delete');
            Route::patch('/pengajuan-cuti/update/{idCuti}', [CutieController::class, 'update'])->name('cutie.pengajuan-cuti.update');
            Route::patch('/pengajuan-cuti/cancel/{idCuti}', [CutieController::class, 'cancel'])->name('cutie.pengajuan-cuti.cancel');
            Route::patch('/pengajuan-cuti/mulai-cuti/{idCuti}', [CutieController::class, 'mulai_cuti'])->name('cutie.pengajuan-cuti.mulai-cuti');
            Route::patch('/pengajuan-cuti/selesai-cuti/{idCuti}', [CutieController::class, 'selesai_cuti'])->name('cutie.pengajuan-cuti.selesai-cuti');
        });

        /** MEMBER CUTI */
        Route::group(['middleware' => ['role:atasan']], function () {
            Route::post('/member-cuti-datatable', [CutieController::class, 'member_cuti_datatable']);
            Route::get('/member-cuti', [CutieController::class, 'member_cuti_view'])->name('cutie.member-cuti');
            Route::post('/member-cuti/store', [CutieController::class, 'store'])->name('cutie.member-cuti.store');
            Route::delete('/member-cuti/delete/{idCuti}', [CutieController::class, 'delete'])->name('cutie.member-cuti.delete');
            Route::patch('/member-cuti/update/{idCuti}', [CutieController::class, 'update'])->name('cutie.member-cuti.update');
            Route::patch('/member-cuti/update-karyawan-pengganti/{idCuti}', [CutieController::class, 'update_karyawan_pengganti'])->name('cutie.member-cuti.update-karyawan-pengganti');
            Route::patch('/member-cuti/reject/{idCuti}', [CutieController::class, 'reject'])->name('cutie.member-cuti.reject');
        });
        Route::patch('/member-cuti/update-dokumen-cuti/{idCuti}', [CutieController::class, 'update_dokumen_cuti'])->name('cutie.member-cuti.update-document-cuti');

        /** PERSONALIA CUTI */
        Route::group(['middleware' => ['role:personalia']], function () {
            Route::post('/personalia-cuti-datatable', [CutieController::class, 'personalia_cuti_datatable']);
            Route::get('/personalia-cuti', [CutieController::class, 'personalia_cuti_view'])->name('cutie.personalia-cuti');
            Route::delete('/personalia-cuti/delete/{idCuti}', [CutieController::class, 'delete'])->name('cutie.personalia-cuti.delete');
            Route::patch('/personalia-cuti/cancel/{idCuti}', [CutieController::class, 'cancel'])->name('cutie.personalia-cuti.cancel');
            Route::patch('/personalia-cuti/reject/{idCuti}', [CutieController::class, 'reject'])->name('cutie.personalia-cuti.reject');

            /** SETTING CUTI */
            Route::post('/setting-cuti-datatable', [CutieController::class, 'setting_cuti_datatable']);
            Route::get('/setting-cuti', [CutieController::class, 'setting_cuti_view'])->name('cutie.setting-cuti');
            Route::delete('/setting-cuti/delete/{idCuti}', [CutieController::class, 'delete_jenis_cuti'])->name('cutie.setting-cuti.delete');
            Route::patch('/setting-cuti/update/{idCuti}', [CutieController::class, 'update_jenis_cuti'])->name('cutie.setting-cuti.update');
            Route::post('/setting-cuti/store', [CutieController::class, 'store_jenis_cuti'])->name('cutie.setting-cuti.store');
        });

        Route::group(['middleware' => ['role:atasan|personalia']], function () {
            /** BYPASS CUTI */
            Route::get('/bypass-cuti', [CutieController::class, 'bypass_cuti_view'])->name('cutie.bypass-cuti');
            Route::post('/bypass-cuti/store', [CutieController::class, 'bypass_store'])->name('cutie.bypass-cuti.store');
        });
    });

    /** LEMBURE */
    Route::group(['prefix' => 'lembure', 'middleware' => ['lembure']], function () {

        // DASHBOARD
        Route::get('/dashboard', [LembureController::class, 'index'])->name('lembure.dashboard')->middleware('role:personalia|atasan');

        // LEADERBOARD LEMBUR
        Route::group(['middleware' => ['role:atasan|personalia']], function () {
            Route::get('/detail-lembur', [LembureController::class, 'detail_lembur_view'])->name('lembure.detail-lembur');
            Route::post('/detail-lembur-datatable', [LembureController::class, 'detail_lembur_datatable']);
            Route::post('/detail-lembur/get-leaderboard-user-monthly', [LembureController::class, 'get_leaderboard_user_monthly']);
        });

        Route::group(['middleware' => ['role:atasan|member']], function () {
            // PENGAJUAN LEMBUR (LEADER)
            Route::get('/pengajuan-lembur', [LembureController::class, 'pengajuan_lembur_view'])->name('lembure.pengajuan-lembur');
            Route::post('/pengajuan-lembur-datatable', [LembureController::class, 'pengajuan_lembur_datatable']);
            Route::post('/pengajuan-lembur/store', [LembureController::class, 'store'])->name('lembure.pengajuan-lembur.store');
            Route::delete('pengajuan-lembur/delete/{idLembur}', [LembureController::class, 'delete'])->name('lembure.pengajuan-lembur.delete');
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
            Route::patch('/approval-lembur/rollback/{idLembur}', [LembureController::class, 'rollback'])->name('lembure.approval-lembur.rollback');
            Route::patch('/approval-lembur/checked-aktual/{idLembur}', [LembureController::class, 'checked_aktual'])->name('lembure.approval-lembur.checked-aktual');
            Route::patch('/approval-lembur/approved-aktual/{idLembur}', [LembureController::class, 'approved_aktual'])->name('lembure.approval-lembur.approved-aktual');
            Route::patch('/approval-lembur/legalized-aktual/{idLembur}', [LembureController::class, 'legalized_aktual'])->name('lembure.approval-lembur.legalized-aktual');
            Route::post('/approval-lembur/get-list-data-cross-check', [LembureController::class, 'get_list_data_cross_check']);

            Route::get('/review-lembur', [LembureController::class, 'review_lembur_view'])->name('lembure.review-lembur');
            Route::post('/review-lembur-datatable', [LembureController::class, 'review_lembur_datatable']);
            Route::patch('/review-lembur/reviewed', [LembureController::class, 'reviewed'])->name('lembure.review-lembur.reviewed');
            Route::patch('/review-lembur/rejected/{idDetailLembur}', [LembureController::class, 'review_lembur_rejected'])->name('lembure.review-lembur.rejected');
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
            Route::post('/export-report-lembur/datatable', [LembureController::class, 'export_slip_lembur_datatable']);
            Route::post('/export-report-lembur/rekap-lembur-perbulan', [LembureController::class, 'export_rekap_lembur_perbulan'])->name('lembure.export-report-lembur.rekap-lembur-perbulan');
            Route::post('/export-report-lembur/slip-lembur-perbulan', [LembureController::class, 'export_slip_lembur_perbulan'])->name('lembure.export-report-lembur.export-slip-lembur-perbulan');
        });

        Route::group(['middleware' => ['role:atasan|personalia']], function (){
            // Bypass Lembur
            Route::get('/bypass-lembur', [LembureController::class, 'bypass_lembur_view'])->name('lembure.bypass-lembur');
            Route::post('/bypass-lembur/store', [LembureController::class, 'bypass_lembur_store'])->name('lembure.bypass-lembur.store');
        });
    });

    /** IZINE */
    Route::group(['prefix' => 'izine', 'middleware' => ['izine']], function () {
        Route::get('/pengajuan-izin', [IzineController::class, 'pengajuan_izin_view'])->name('izine.pengajuan-izin');
        Route::post('/pengajuan-izin-datatable', [IzineController::class, 'pengajuan_izin_datatable']);
        Route::post('/pengajuan-izin/store', [IzineController::class, 'store'])->name('izine.pengajuan-izin.store');
        Route::delete('/pengajuan-izin/delete/{idIzin}', [IzineController::class, 'delete'])->name('izine.pengajuan-izin.delete');
        Route::patch('/pengajuan-izin/update/{idIzin}', [IzineController::class, 'update'])->name('izine.pengajuan-izin.update');
        Route::patch('/pengajuan-izin/done/{idIzin}', [IzineController::class, 'done'])->name('izine.pengajuan-izin.done');

        Route::get('/lapor-skd', [SakiteController::class, 'lapor_skd_view'])->name('izine.lapor-skd');
        Route::post('/lapor-skd-datatable', [SakiteController::class, 'lapor_skd_datatable']);
        Route::post('/lapor-skd/store', [SakiteController::class, 'store'])->name('izine.lapor-skd.store');
        Route::delete('/lapor-skd/delete/{idSakit}', [SakiteController::class, 'delete'])->name('izine.lapor-skd.delete');
        Route::patch('/lapor-skd/update/{idSakit}', [SakiteController::class, 'update'])->name('izine.lapor-skd.update');

        //LOG BOOK
        Route::group(['middleware' => ['role:security']], function () {
            Route::get('/log-book-izin', [IzineController::class, 'log_book_izin_view'])->name('izine.log-book-izin');
            Route::post('/log-book-izin-datatable', [IzineController::class, 'log_book_izin_datatable']);
            Route::patch('/log-book-izin/confirmed/{idIzin}', [IzineController::class, 'confirmed'])->name('izine.lapor-skd.confirmed');
        });

        Route::group(['middleware' => ['role:atasan|personalia']], function () {
            //IZIN
            Route::get('/approval-izin', [IzineController::class, 'approval_izin_view'])->name('izine.approval-izin');
            Route::post('/approval-izin-datatable', [IzineController::class, 'approval_izin_datatable']);
            Route::patch('/approval-izin/checked/{idIzin}', [IzineController::class, 'checked'])->name('izine.approval-izin.checked');
            Route::patch('/approval-izin/approved/{idIzin}', [IzineController::class, 'approved'])->name('izine.approval-izin.approved');
            Route::patch('/approval-izin/legalized/{idIzin}', [IzineController::class, 'legalized'])->name('izine.approval-izin.legalized');
            Route::patch('/approval-izin/rejected/{idIzin}', [IzineController::class, 'rejected'])->name('izine.approval-izin.rejected');

            //SKD
            Route::get('/approval-skd', [SakiteController::class, 'approval_skd_view'])->name('izine.approval-skd');
            Route::post('/approval-skd-datatable', [SakiteController::class, 'approval_skd_datatable']);
            Route::patch('/approval-skd/approved/{idIzin}', [SakiteController::class, 'approved'])->name('izine.approval-skd.approved');
            Route::patch('/approval-skd/legalized/{idIzin}', [SakiteController::class, 'legalized'])->name('izine.approval-skd.legalized');
            Route::patch('/approval-skd/rejected/{idIzin}', [SakiteController::class, 'rejected'])->name('izine.approval-skd.rejected');

            //EXPORT
            Route::get('/export', [IzineController::class, 'export_view'])->name('izine.export');
            Route::post('/export/export-izin-dan-skd', [IzineController::class, 'export_izin_dan_skd'])->name('izine.export.export-izin-dan-skd');
        });

        Route::group(['middleware' => ['role:personalia']], function () {
            Route::get('/piket', [IzineController::class, 'piket_view'])->name('izine.piket');
            Route::post('/piket-datatable', [IzineController::class, 'piket_datatable']);
            Route::post('/piket/store', [IzineController::class, 'piket_store'])->name('izine.piket.store');
            Route::patch('/piket/update/{idPiket}', [IzineController::class, 'piket_update'])->name('izine.piket.update');
            Route::delete('/piket/delete/{idPiket}', [IzineController::class, 'piket_delete'])->name('izine.piket.delete');
        });
    });

      /** ATTENDANCE */
    Route::group(['prefix' => 'attendance'], function () {
        Route::group(['middleware' => ['role:personalia|admin-dept']], function () {
            Route::get('/dashboard', [AttendanceDashboardController::class, 'index'])->name('attendance.dashboard');

            Route::group(['middleware' => ['role:personalia']], function () {
                // SCANLOG
                Route::get('/scanlog', [ScanlogController::class, 'index'])->name('attendance.scanlog');
                Route::post('/scanlog/datatable', [ScanlogController::class, 'datatable']);
                Route::post('/scanlog/download-scanlog', [ScanlogController::class, 'download_scanlog'])->name('attendance.scanlog.download-scanlog');
                Route::post('/scanlog/export-scanlog', [ScanlogController::class, 'export_scanlog'])->name('attendance.scanlog.export-scanlog');

                // DEVICE
                Route::get('/device', [DeviceController::class, 'index'])->name('attendance.device');
                Route::post('/device/datatable', [DeviceController::class, 'datatable']);
                Route::post('/device/store', [DeviceController::class, 'store'])->name('attendance.device.store');
                Route::patch('/device/update/{idDevice}', [DeviceController::class, 'update'])->name('attendance.device.update');
                Route::delete('/device/delete/{idDevice}', [DeviceController::class, 'delete'])->name('attendance.device.delete');

                // APPROVAL
                Route::get('/approval', [ATTApprovalController::class, 'index'])->name('attendance.approval');
                Route::post('/approval/datatable', [ATTApprovalController::class, 'datatable']);
                Route::patch('/approval/legalized/{idAttGps}', [ATTApprovalController::class, 'legalized'])->name('attendance.approval.legalized');
            });

            // SHIFT GROUP
            Route::get('/shift-group', [ShiftgroupController::class, 'index'])->name('attendance.shiftgroup');
            Route::post('/shift-group/datatable', [ShiftgroupController::class, 'datatable']);
            Route::post('/shift-group/store', [ShiftgroupController::class, 'store'])->name('attendance.shiftgroup.store');
            Route::patch('/shift-group/update/{idKaryawan}', [ShiftgroupController::class, 'update'])->name('attendance.shiftgroup.update');

            // PRESENSI
            Route::get('/presensi', [PresensiController::class, 'index'])->name('attendance.presensi');
            Route::post('/presensi/datatable', [PresensiController::class, 'datatable']);
            Route::post('/presensi/check-presensi', [PresensiController::class, 'check_presensi'])->name('attendance.presensi.check-presensi');

            // REKAP
            Route::get('/rekap', [RekapController::class, 'index'])->name('attendance.rekap');
            Route::post('/rekap/export-rekap', [RekapController::class, 'export_rekap'])->name('attendance.rekap.export-rekap');
        });

        // GPS
        Route::get('/gps', [AttendanceGpsController::class, 'index'])->name('attendance.gps');
        Route::get('/gps/get-att-gps-list', [AttendanceGpsController::class, 'get_att_gps_list']);
        Route::post('/gps/datatable', [AttendanceGpsController::class, 'datatable']);
        Route::post('/gps/store', [AttendanceGpsController::class, 'store'])->name('attendance.gps.store');
    });

    /** TUGASLUARE */
    Route::group(['prefix' => 'tugasluare', 'middleware' => ['tugasluare']], function () {
        // PENGAJUAN TL
        Route::group(['middleware' => ['role:atasan|member']], function () {
            Route::get('/pengajuan', [TLPengajuanController::class, 'index'])->name('tugasluare.pengajuan');
            Route::post('/pengajuan/datatable', [TLPengajuanController::class, 'datatable']);
            Route::post('/pengajuan/store', [TLPengajuanController::class, 'store'])->name('tugasluare.pengajuan.store');
            Route::patch('/pengajuan/update/{idTugasLuar}', [TLPengajuanController::class, 'update'])->name('tugasluare.pengajuan.update');
            Route::delete('/pengajuan/delete/{idTugasLuar}', [TLPengajuanController::class, 'destroy'])->name('tugasluare.pengajuan.delete');
            Route::patch('/pengajuan/verifikasi/{idTugasLuar}', [TLPengajuanController::class, 'verifikasi'])->name('tugasluare.pengajuan.verifikasi');
            Route::patch('/pengajuan/aktual/{idTugasLuar}', [TLPengajuanController::class, 'aktual'])->name('tugasluare.pengajuan.aktual');
        });

        // APPROVAL TL
        Route::group(['middleware' => ['role:atasan|personalia|security']], function () {
            Route::get('/approval', [TLApprovalController::class, 'index'])->name('tugasluare.approval');
            Route::post('/approval/datatable', [TLApprovalController::class, 'datatable']);
            Route::patch('/approval/checked/{idTugasLuar}', [TLApprovalController::class, 'checked'])->name('tugasluare.approval.checked');
            Route::patch('/approval/legalized/{idTugasLuar}', [TLApprovalController::class, 'legalized'])->name('tugasluare.approval.legalized');
            Route::patch('/approval/known/{idTugasLuar}', [TLApprovalController::class, 'known'])->name('tugasluare.approval.known');
            Route::patch('/approval/rejected/{idTugasLuar}', [TLApprovalController::class, 'rejected'])->name('tugasluare.approval.rejected');
            Route::delete('/approval/delete/{idTugasLuar}', [TLApprovalController::class, 'destroy'])->name('tugasluare.approval.delete');
        });
    });

    /** SECURITY */
    Route::group(['prefix' => 'security', 'middleware' => ['role:security']], function () {
        Route::get('/', [SecurityController::class, 'index'])->name('security.index');
        Route::patch('/confirmed/{id}', [SecurityController::class, 'confirmed']);
        Route::get('/get-qr-detail/{id}', [SecurityController::class, 'get_qr_detail']);
        Route::post('/datatable/izin', [SecurityController::class, 'izin_datatable']);
        Route::post('/datatable/tugasluar', [SecurityController::class, 'tugasluar_datatable']);
    });

    /** KSK */
    Route::group(['prefix' => 'ksk'], function () {
        Route::group(['middleware' => ['role:personalia', 'ksk']], function () {
            Route::get('/release', [KSKReleaseController::class, 'index'])->name('ksk.release');
            Route::post('/release/datatable-unreleased', [KSKReleaseController::class, 'datatable_unreleased']);
            Route::post('/release/datatable-released', [KSKReleaseController::class, 'datatable_released']);
            Route::delete('/release/delete/{idKsk}', [KSKReleaseController::class, 'destroy'])->name('ksk.release.delete');
            Route::post('/release/store', [KSKReleaseController::class, 'store'])->name('ksk.release.store');
            Route::patch('/release/update-detail-ksk/{idDetailKsk}', [KSKReleaseController::class, 'update_detail_ksk'])->name('ksk.release.update-detail-ksk');

            Route::get('/setting', [KSKSettingController::class, 'index'])->name('ksk.setting');
            Route::post('/setting/update', [KSKSettingController::class, 'update'])->name('ksk.setting.update');

            Route::group(['prefix' => 'ajax'], function () {
                Route::post('/release/get-karyawans', [KSKAjaxController::class, 'get_karyawans']);
                Route::get('/release/get-detail-ksk/{idKSK}', [KSKAjaxController::class, 'get_detail_ksk_release']);
                Route::get('/release/get-ksk/{idKSK}', [KSKAjaxController::class, 'get_ksk']);
            });
        });

        Route::group(['middleware' => ['role:atasan|personalia']], function () {
            Route::get('/approval', [KSKApprovalController::class, 'index'])->name('ksk.approval')->middleware('ksk');
            Route::post('/approval/datatable-must-approved', [KSKApprovalController::class, 'datatable_must_approved']);
            Route::post('/approval/datatable-history', [KSKApprovalController::class, 'datatable_history']);
            Route::delete('/approval/delete/{idKsk}', [KSKApprovalController::class, 'destroy'])->name('ksk.approval.delete');
            Route::patch('/approval/update-detail-ksk/{idDetailKsk}', [KSKApprovalController::class, 'update_detail_ksk'])->name('ksk.approval.update-detail-ksk');
            Route::patch('/approval/approve/{idKSK}', [KSKApprovalController::class, 'approve'])->name('ksk.approval.approve');
            Route::patch('/approval/legalize/{idKSK}', [KSKApprovalController::class, 'legalize'])->name('ksk.approval.legalize')->middleware('role:personalia');
        });

        Route::group(['prefix' => 'ajax'], function () {
            Route::get('/approval/get-ksk/{idKSK}', [KSKAjaxController::class, 'get_approval_ksk']);
            Route::get('/approval/get-detail-ksk/{idKSK}', [KSKAjaxController::class, 'get_detail_ksk_approval']);
        });

        Route::group(['prefix' => 'cleareance'], function () {
            Route::group(['middleware' => ['role:personalia']], function () {
                Route::get('/release', [KSKReleaseCleareanceController::class, 'index'])->name('ksk.cleareance.release')->middleware('ksk');
                Route::post('/release/datatable-unreleased', [KSKReleaseCleareanceController::class, 'datatable_unreleased']);
                Route::post('/release/datatable-released', [KSKReleaseCleareanceController::class, 'datatable_released']);
                Route::patch('/release/update/{idDetailKSK}', [KSKReleaseCleareanceController::class, 'update'])->name('ksk.cleareance.update');
                Route::patch('/release/rollback/{idCleareanceDetail}', [KSKReleaseCleareanceController::class, 'rollback'])->name('ksk.cleareance.rollback');
                // Route::post('/release/datatable-released', [ReleaseController::class, 'datatable_released']);
                // Route::delete('/release/delete/{idKsk}', [ReleaseController::class, 'destroy'])->name('ksk.release.delete');
                // Route::patch('/release/update-detail-ksk/{idDetailKsk}', [ReleaseController::class, 'update_detail_ksk'])->name('ksk.release.update-detail-ksk');


                Route::group(['prefix' => 'ajax'], function () {
                    Route::post('/release/get-karyawans', [KSKAjaxCleareanceController::class, 'select_get_karyawans']);
                    Route::post('/release/get-atasan-langsung', [KSKAjaxCleareanceController::class, 'select_get_atasan_langsung']);
                    Route::get('/release/get-detail-cleareance/{idCleareance}', [KSKAjaxCleareanceController::class, 'get_detail_cleareance']);
                    // Route::get('/release/get-detail-ksk/{idKSK}', [KSKAjaxController::class, 'get_detail_ksk_release']);
                    // Route::get('/release/get-ksk/{idKSK}', [KSKAjaxController::class, 'get_ksk']);
                });
            });

            Route::group(['middleware' => ['role:atasan']], function () {
                Route::get('/approval', [KSKApprovalCleareanceController::class, 'index'])->name('ksk.cleareance.approval')->middleware('ksk');
                Route::post('/approval/datatable-must-approved', [KSKApprovalCleareanceController::class, 'datatable_must_approved']);
                Route::post('/approval/datatable-history', [KSKApprovalCleareanceController::class, 'datatable_history']);
                // Route::delete('/approval/delete/{idKsk}', [KSKApprovalController::class, 'destroy'])->name('ksk.approval.delete');
                // Route::patch('/approval/update-detail-ksk/{idDetailKsk}', [KSKApprovalController::class, 'update_detail_ksk'])->name('ksk.approval.update-detail-ksk');
                // Route::patch('/approval/approve/{idKSK}', [KSKApprovalController::class, 'approve'])->name('ksk.approval.approve');
                // Route::patch('/approval/legalize/{idKSK}', [KSKApprovalController::class, 'legalize'])->name('ksk.approval.legalize')->middleware('role:personalia');
            });
        });
    });
});

// STOCK-OPNAME
// Route::group(['prefix' => 'sto', 'middleware' => ['auth']], function () {

//     //REGISTER LABEL
//     Route::get('/input_label', [StoController::class, 'input_label'])->name('sto.input-label');
//     Route::post('/input_label/post', [StoController::class, 'store_label'])->name('sto.store-label');
//     Route::post('/input_label/datatable', [StoController::class, 'label_datatable']);

//     //HASIL STO
//     Route::get('/input_hasil', [StoController::class, 'input_hasil'])->name('sto.input-hasil');
//     Route::get('/input_hasil/get_sto_line/{idStoLine}', [StoController::class, 'get_sto_line'])->name('sto.get-sto-line');
//     Route::post('/input_hasil/datatable', [StoController::class, 'hasil_datatable']);
//     Route::get('/input_hasil/get_part/{part_code}', [StoController::class, 'get_part'])->name('sto.get-part');
//     Route::post('/input_hasil/get_part', [StoController::class, 'get_part_code'])->name('sto.get-part-code');
//     Route::post('/input_hasil/get_customer', [StoController::class, 'get_customer'])->name('sto.get-customer');
//     Route::post('/input_hasil/get_no_label', [StoController::class, 'get_no_label'])->name('sto.get-no-label');
//     Route::get('/input_hasil/get_wh/{whId}', [StoController::class, 'get_warehouse'])->name('sto.get-warehouse');
//     Route::post('/input_hasil/get_wh_label/', [StoController::class, 'get_wh_label'])->name('sto.get-wh-label');
//     Route::post('/input_hasil/post', [StoController::class, 'store_hasil'])->name('sto.store-hasil');
//     Route::delete('/delete/data_hasil/{idStoLine}', [StoController::class, 'delete'])->name('sto.delete-data');
//     Route::patch('/data-sto/update/{idStoLine}', [StoController::class, 'update'])->name('sto.update-data');


//     //COMPARE
//     Route::get('/compare', [StoReportController::class, 'compare'])->name('sto.compare');
//     Route::post('/compare/datatable', [StoReportController::class, 'datatable'])->name('sto.datatable');
//     Route::post('/compare/export-excel', [StoReportController::class, 'export'])->name('sto.datatable-export');
// });

// /**testing controller */
// Route::get('/test', [TestController::class, 'index']);
// Route::get('/getsto', [TestController::class, 'getSto']);
// Route::get('/testlogout', [TestController::class, 'logout']);
