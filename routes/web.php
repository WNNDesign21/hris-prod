<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterData\GrupController;
use App\Http\Controllers\MasterData\SeksiController;
use App\Http\Controllers\MasterData\DivisiController;
use App\Http\Controllers\MasterData\JabatanController;
use App\Http\Controllers\MasterData\DashboardController;
use App\Http\Controllers\MasterData\DepartemenController;
use App\Http\Controllers\MasterData\OrganisasiController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

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
});
