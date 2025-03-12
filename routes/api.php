<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Attendance\WebhookController;

Route::post('/webhook/attendance/scanlog/{idOrganisasi}', [WebhookController::class, 'get_att_tcf']);
Route::get('/webhook/attendance/scanlog/test', [WebhookController::class, 'test']);
