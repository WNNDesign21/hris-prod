<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterData\DashboardController;
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

