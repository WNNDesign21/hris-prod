<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterData\DashboardController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
   
Route::get('/home', [HomeController::class, 'index'])->name('root');

/** MASTER DATA */
Route::get('/master-data/dashboard',[DashboardController::class, 'index'])->name('master-data.dashboard');
