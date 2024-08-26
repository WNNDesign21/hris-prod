<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterData\AkunController;
use App\Http\Controllers\MasterData\GrupController;
use App\Http\Controllers\MasterData\SeksiController;
use App\Http\Controllers\MasterData\DivisiController;
use App\Http\Controllers\MasterData\PosisiController;
use App\Http\Controllers\MasterData\JabatanController;
use App\Http\Controllers\MasterData\KontrakController;
use App\Http\Controllers\MasterData\KaryawanController;
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

/** MASTER DATA FEATURE */
Route::group(['middleware' => ['auth']], function () {
    // MENU UTAMA
    Route::get('/home', [HomeController::class, 'index'])->name('root');

    /** MASTER DATA - DASHBOARD */
    Route::get('/master-data/dashboard',[DashboardController::class, 'index'])->name('master-data.dashboard');

    /** MASTER DATA - ORGANISASI */
    Route::post('/master-data/organisasi/datatable', [OrganisasiController::class, 'datatable']);
    Route::get('/master-data/organisasi',[OrganisasiController::class, 'index'])->name('master-data.organisasi');
    Route::post('/master-data/organisasi/store',[OrganisasiController::class, 'store'])->name('master-data.organisasi.store');
    Route::delete('/master-data/organisasi/delete/{idOrganisasi}', [OrganisasiController::class, 'delete'])->name('master-data.organisasi.delete');
    Route::patch('/master-data/organisasi/update/{idOrganisasi}', [OrganisasiController::class, 'update'])->name('master-data.organisasi.update');

    /** MASTER DATA - DIVISI */
    Route::post('/master-data/divisi/datatable', [DivisiController::class, 'datatable']);
    Route::get('/master-data/divisi',[DivisiController::class, 'index'])->name('master-data.divisi');
    Route::post('/master-data/divisi/store',[DivisiController::class, 'store'])->name('master-data.divisi.store');
    Route::delete('/master-data/divisi/delete/{idDivisi}', [DivisiController::class, 'delete'])->name('master-data.divisi.delete');
    Route::patch('/master-data/divisi/update/{idDivisi}', [DivisiController::class, 'update'])->name('master-data.divisi.update');

    /** MASTER DATA - DEPARTEMEN */
    Route::post('/master-data/departemen/datatable', [DepartemenController::class, 'datatable']);
    Route::get('/master-data/departemen',[DepartemenController::class, 'index'])->name('master-data.departemen');
    Route::post('/master-data/departemen/store',[DepartemenController::class, 'store'])->name('master-data.departemen.store');
    Route::delete('/master-data/departemen/delete/{idDepartemen}', [DepartemenController::class, 'delete'])->name('master-data.departemen.delete');
    Route::patch('/master-data/departemen/update/{idDepartemen}', [DepartemenController::class, 'update'])->name('master-data.departemen.update');

    /** MASTER DATA - SEKSI */
    Route::post('/master-data/seksi/datatable', [SeksiController::class, 'datatable']);
    Route::get('/master-data/seksi',[SeksiController::class, 'index'])->name('master-data.seksi');
    Route::post('/master-data/seksi/store',[SeksiController::class, 'store'])->name('master-data.seksi.store');
    Route::delete('/master-data/seksi/delete/{idSeksi}', [SeksiController::class, 'delete'])->name('master-data.seksi.delete');
    Route::patch('/master-data/seksi/update/{idSeksi}', [SeksiController::class, 'update'])->name('master-data.seksi.update');

    /** MASTER DATA - GRUP */
    Route::post('/master-data/grup/datatable', [GrupController::class, 'datatable']);
    Route::get('/master-data/grup',[GrupController::class, 'index'])->name('master-data.grup');
    Route::post('/master-data/grup/store',[GrupController::class, 'store'])->name('master-data.grup.store');
    Route::delete('/master-data/grup/delete/{idGrup}', [GrupController::class, 'delete'])->name('master-data.grup.delete');
    Route::patch('/master-data/grup/update/{idGrup}', [GrupController::class, 'update'])->name('master-data.grup.update');

    
    /** MASTER DATA - JABATAN */
    Route::post('/master-data/jabatan/datatable', [JabatanController::class, 'datatable']);
    Route::get('/master-data/jabatan',[JabatanController::class, 'index'])->name('master-data.jabatan');
    Route::post('/master-data/jabatan/store',[JabatanController::class, 'store'])->name('master-data.jabatan.store');
    Route::delete('/master-data/jabatan/delete/{idJabatan}', [JabatanController::class, 'delete'])->name('master-data.jabatan.delete');
    Route::patch('/master-data/jabatan/update/{idJabatan}', [JabatanController::class, 'update'])->name('master-data.jabatan.update');

    /** MASTER DATA - POSISI */
    Route::post('/master-data/posisi/datatable', [PosisiController::class, 'datatable']);
    Route::get('/master-data/posisi',[PosisiController::class, 'index'])->name('master-data.posisi');
    Route::post('/master-data/posisi/store',[PosisiController::class, 'store'])->name('master-data.posisi.store');
    Route::delete('/master-data/posisi/delete/{idPosisi}', [PosisiController::class, 'delete'])->name('master-data.posisi.delete');
    Route::patch('/master-data/posisi/update/{idPosisi}', [PosisiController::class, 'update'])->name('master-data.posisi.update');

    /** MASTER DATA - KARYAWAN */
    Route::post('/master-data/karyawan/datatable', [KaryawanController::class, 'datatable']);
    Route::get('/master-data/karyawan',[KaryawanController::class, 'index'])->name('master-data.karyawan');
    Route::post('/master-data/karyawan/store',[KaryawanController::class, 'store'])->name('master-data.karyawan.store');
    Route::delete('/master-data/karyawan/delete/{idKaryawan}', [KaryawanController::class, 'delete'])->name('master-data.karyawan.delete');
    Route::patch('/master-data/karyawan/update/{idKaryawan}', [KaryawanController::class, 'update'])->name('master-data.karyawan.update');
    Route::post('/master-data/akun/store-or-update',[AkunController::class, 'store_or_update'])->name('master-data.akun.storeUpdate');
    
    /** MASTER DATA - KONTRAK */
    Route::post('/master-data/kontrak/datatable', [KontrakController::class, 'datatable']);
    Route::get('/master-data/kontrak',[KontrakController::class, 'index'])->name('master-data.kontrak');
    Route::post('/master-data/kontrak/store',[KontrakController::class, 'store'])->name('master-data.kontrak.store');
    Route::delete('/master-data/kontrak/delete/{idKontrak}', [KontrakController::class, 'delete'])->name('master-data.kontrak.delete');
    Route::patch('/master-data/kontrak/update/{idKontrak}', [KontrakController::class, 'update'])->name('master-data.kontrak.update');
    Route::post('/master-data/kontrak/store-or-update',[KontrakController::class, 'store_or_update'])->name('master-data.kontrak.storeUpdate');
    Route::post('/master-data/kontrak/upload-kontrak/{idKontrak}',[KontrakController::class, 'upload_kontrak'])->name('master-data.kontrak.uploadKontrak');
});



