<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Cutie\CutieController;
use App\Http\Controllers\MasterData\AkunController;
use App\Http\Controllers\MasterData\GrupController;
use App\Http\Controllers\MasterData\SeksiController;
use App\Http\Controllers\MasterData\DivisiController;
use App\Http\Controllers\MasterData\ExportController;
use App\Http\Controllers\MasterData\PosisiController;
use App\Http\Controllers\MasterData\JabatanController;
use App\Http\Controllers\MasterData\KontrakController;
use App\Http\Controllers\MasterData\KaryawanController;
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

/** MASTER DATA FEATURE */
Route::group(['middleware' => ['auth']], function () {
    // MENU UTAMA
    Route::get('/home', [HomeController::class, 'index'])->name('root');

    Route::group(['prefix' => 'master-data'], function () {
        /** MASTER DATA - DASHBOARD */
        Route::get('/dashboard',[DashboardController::class, 'index'])->name('master-data.dashboard');
        Route::get('/dashboard/get-data-karyawan-dashboard',[DashboardController::class, 'get_data_karyawan_dashboard']);
        Route::get('/dashboard/get-data-turnover-monthly-dashboard',[DashboardController::class, 'get_data_turnover_monthly_dashboard']);
        Route::get('/dashboard/get-data-turnover-detail-monthly-dashboard',[DashboardController::class, 'get_data_turnover_detail_monthly_dashboard']);
        Route::get('/dashboard/get-data-kontrak-progress-dashboard',[DashboardController::class, 'get_data_kontrak_progress_dashboard']);
        Route::get('/dashboard/get-data-keluar-masuk-karyawan-dashboard',[DashboardController::class, 'get_data_keluar_masuk_karyawan_dashboard']);
        Route::get('/dashboard/get-total-data-karyawan-by-status-karyawan-dashboard',[DashboardController::class, 'get_total_data_karyawan_by_status_karyawan_dashboard']);
    
        /** MASTER DATA - ORGANISASI */
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
        Route::post('/kontrak/done/{idKontrak}',[KontrakController::class, 'done_kontrak'])->name('master-data.kontrak.done');

        /** MASTER DATA - EXPORT */
        Route::get('/export',[ExportController::class, 'index'])->name('master-data.export');
        Route::post('/export/export-master-data',[ExportController::class, 'export_master_data'])->name('master-data.export.master-data');
        Route::post('/export/export-kontrak',[ExportController::class, 'export_kontrak'])->name('master-data.export.kontrak');

        /** MASTER DATA - TURNOVER */
        Route::post('/turnover/datatable', [TurnoverController::class, 'datatable']);
        Route::get('/turnover',[TurnoverController::class, 'index'])->name('master-data.turnover');
        Route::post('/turnover/store',[TurnoverController::class, 'store'])->name('master-data.turnover.store');
    });

    Route::group(['prefix' => 'cutie'], function () {
        Route::post('/pengajuan-cuti-datatable', [CutieController::class, 'pengajuan_cuti_datatable']);
        Route::get('/dashboard',[CutieController::class, 'index'])->name('cutie.dashboard');
        Route::get('/pengajuan-cuti',[CutieController::class, 'pengajuan_cuti_view'])->name('cutie.pengajuan-cuti');
        Route::post('/pengajuan-cuti/store',[CutieController::class, 'store'])->name('cutie.pengajuan-cuti.store');
        Route::delete('/pengajuan-cuti/delete/{idCuti}',[CutieController::class, 'delete'])->name('cutie.pengajuan-cuti.delete');
        Route::patch('/pengajuan-cuti/update/{idCuti}',[CutieController::class, 'update'])->name('cutie.pengajuan-cuti.update');
     });

    

    
});



