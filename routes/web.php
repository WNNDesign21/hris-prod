<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
   
Route::get('/home', [HomeController::class, 'index'])->name('root');
