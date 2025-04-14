<?php

namespace App\Http\Controllers\KSK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TindakLanjutController extends Controller
{
    public function index()
    {
        // return redirect()->route('under-maintenance');
        $dataPage = [
            'pageTitle' => "KSK-E - Tindak Lanjut KSK",
            'page' => 'ksk-tindak-lanjut',
        ];
        return view('pages.ksk-e.tindak-lanjut.index', $dataPage);
    }
}
