<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterData\DivisiController;
use App\Http\Controllers\MasterData\DashboardController;
use App\Http\Controllers\MasterData\DepartemenController;
use App\Http\Controllers\MasterData\OrganisasiController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
   
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

