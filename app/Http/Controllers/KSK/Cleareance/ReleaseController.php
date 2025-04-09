<?php

namespace App\Http\Controllers\KSK\Cleareance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    public function index()
    {
        $dataPage = [
            'pageTitle' => "KSK-E - Release Cleareance",
            'page' => 'ksk-cleareance-release',
        ];
        return view('pages.ksk-e.cleareance.release.index', $dataPage);
    }
}
